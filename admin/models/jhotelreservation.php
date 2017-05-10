<?php
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');

class JHotelReservationModelJHotelReservation extends JModelAdmin
{

    public function getForm($data = array(), $loadData = true)
    {}

    /**
     * @return stdClass class of all statistics(reservations made,active offers, booked offers monthly, income total,monthly) used in the dashboard
     */
    public function getStatistics(){
        $statistics = new stdClass();
        $hotelIds = $this->getHotelIds();

        $reservations =JTable::getInstance('Confirmations','Table');

        $statistics->totalMonthlyReservation = $reservations->getMonthReservations($hotelIds);
        $currency_id = $this->getCurrency();
        $otherCurrencies = $reservations->getCurrencies($currency_id->description);

        $statistics->monthlyIncome = (float)$reservations->getMonthlyIncomeReservations($currency_id->description,$hotelIds);
        $statistics->totalIncome = (float)$reservations->getTotalIncomeReservations($currency_id->description,$hotelIds);
        $incomeMonthly = 0;
        $incomeTotal = 0;

        if(isset($otherCurrencies) && count($otherCurrencies)>0) {
            $incomeFromOtherCurrencies = $this->getAllCurrenciesIncome();
            //Converted values
            //sum up the converted values together
            foreach ($incomeFromOtherCurrencies as $key => $incomeFromOtherCurrency) {
                $incomeTotal += (float)$incomeFromOtherCurrency->totalIncomeCurrency;
                $incomeMonthly += (float)$incomeFromOtherCurrency->monthlyIncomeCurrency;
            }
        }

        //assign the converted values to the default currency value
        $statistics->monthlyIncome += $incomeMonthly;
        $statistics->totalIncome += $incomeTotal;


        $statistics->totalReservations = $reservations->getTotalReservations($hotelIds);
        $statistics->monthlyBookedOffers = $reservations->getBookedOffers($hotelIds,true);
        $statistics->bookedOffers = $reservations->getBookedOffers($hotelIds);

        $offersTable = JTable::getInstance('Offers','JTable');
        $statistics->totalOffers = $offersTable->getTotalNumberOffers();
        $statistics->activeOffers = $offersTable->getTotalActiveOffers();

        return $statistics;
    }

    /**
     * Get selected Currency from application settings
     * @return mixed Currency Object
     */
    public function getCurrency(){
        $currency = JHotelUtil::getApplicationSettings();
        $currencySelected = CurrencyService::getCurrency($currency->currency_id);
        return $currencySelected;
    }

    /**
     * @return array of object of the income (monthly,total) amounts that are not in the default currency
     *         but rather converted to the default currency
     */
    private function getAllCurrenciesIncome(){
        $allIncomeCurrency = array();
        $reservations =JTable::getInstance('Confirmations','Table');
        $appSettingsCurrency = $this->getCurrency();
        $otherCurrencies = $reservations->getCurrencies($appSettingsCurrency->description);

        $hotelIds = $this->getHotelIds();

        //Loop only the currencies that the amount is paid and need to convert to the default currency
        foreach($otherCurrencies as $currency) {
            if ($currency->description != $appSettingsCurrency->description) {
                $statistics = new stdClass();

                //get the values from other currencies not the default one
                $statistics->monthlyIncomeCurrency = $reservations->getMonthlyIncomeReservations($currency->description,$hotelIds);
                $statistics->totalIncomeCurrency = $reservations->getTotalIncomeReservations($currency->description,$hotelIds);

                //convert the string value to float
                $statistics->monthlyIncomeCurrency = (float)$statistics->monthlyIncomeCurrency;
                $statistics->totalIncomeCurrency = (float)$statistics->totalIncomeCurrency;

                //convert the value to the current curency being used (default)
                $statistics->monthlyIncomeCurrency = CurrencyService::convertCurrency($statistics->monthlyIncomeCurrency,$currency->description,$appSettingsCurrency->description);
                $statistics->totalIncomeCurrency  = CurrencyService::convertCurrency($statistics->totalIncomeCurrency,$currency->description,$appSettingsCurrency->description);

                //round and turn float
                $statistics->totalIncomeCurrency = round((float) $statistics->totalIncomeCurrency,2);
                $statistics->monthlyIncomeCurrency = round((float)$statistics->monthlyIncomeCurrency,2);

                $allIncomeCurrency[$currency->description] = $statistics;
            }
        }
        return $allIncomeCurrency;
    }

    /**
     * Get news from rss feeds
     * @return array of news being fetch
     */
    public function getServerNews() {
        $rss = new DOMDocument();
        $rss->load('http://www.cmsjunkie.com/blog/rss/');

        $feeds = array();
        foreach ($rss->getElementsByTagName('item') as $node) {
            $item = array (
                'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
                'description' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'publish_date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
            );
            array_push($feeds, $item);
        }
        return $feeds;
    }

    /**
     * Get the latest news from local database and prepare them
     *
     * @param unknown_type $limit
     */
    public function getLocalNews($limit=3) {
        // $limit -> the limit of the news to be displayed in the dashboard
        $newsTable = JTable::getInstance('News','JTable');
        $news = $newsTable->getLocalNews($limit);

        foreach($news as $item){
            $publish_ago = JHotelUtil::convertTimestampToAgo($item->publish_date);
            $item->publish_ago = $publish_ago;

            $time = strftime('%Y-%m-%d',(strtotime('7 days ago')));
            $retrieve_date = strftime('%Y-%m-%d',(strtotime($item->retrieve_date)));
            //For 7 days from the moment of the retrieve_date the news will be displayed like NEW
            if($time < $retrieve_date) {
                $item->new = true;
            }

            $item->description = mb_strimwidth(strip_tags($item->description), 0, 200, '...');
            $item->publishDateS = date('l, M d, Y', strtotime($item->publish_date));
        }

        return $news;
    }

    /**
     * Retrieve the last news from database
     *
     */
    public function getLocalLastNews() {
        $newsTable = JTable::getInstance('News','JTable');
        $lastNews = $newsTable->getLocalLastNews();
        return $lastNews;
    }

    /**
     * Get the latest news from server and store the new ones
     *
     */
    public function getLatestServerNews() {
        $lastNews = $this->getLocalLastNews();

        if (empty($lastNews)) {
            $serverNews = $this->getServerNews();
            $this->storeNews($serverNews);
        }else{
            $days_ago = NEWS_REFRESH_PERIOD; // refresh records after specified days
            $check_date = date('Y-m-d H:i:s',(strtotime($days_ago.' days ago')));
            $lastNewsRetrieveDate = date('Y-m-d H:i:s', strtotime($lastNews->retrieve_date));

            if($check_date > $lastNewsRetrieveDate) {
                $serverNews = $this->getServerNews();
                $localNews = $this->getLocalNews();

                $feeds = array();
                foreach($serverNews as $singleServerNews) {
                    $title = str_replace(' & ', ' &amp; ', $singleServerNews['title']);
                    $link = $singleServerNews['link'];
                    $description = $singleServerNews['description'];
                    $publish_date = date('Y-m-d H:i:s', strtotime($singleServerNews['publish_date']));

                    $flag = true;
                    foreach ($localNews as $singleLocalNews) {
                        $singleLocalNews_publish_date = date('Y-m-d H:i:s', strtotime($singleLocalNews->publish_date));
                        if($publish_date == $singleLocalNews_publish_date) {
                            $flag = false;
                        }
                    }

                    if($flag) {
                        $item = array (
                            'title' => $title,
                            'link' => $link,
                            'description' => $description,
                            'publish_date' => $publish_date
                        );
                        array_push($feeds, $item);
                    }
                }

                //if there are new news store them
                if(!empty($feeds)) {
                    $this->storeNews($feeds);
                    return $this->getLocalNews(3);
                }
            }
        }
    }

    /**
     * Store the news into database
     *
     * @param unknown_type $feeds
     */
    public function storeNews($feeds) {
        foreach($feeds as $feed) {
            $title = str_replace(' & ', ' &amp; ', $feed['title']);
            $link = $feed['link'];
            $description = $feed['description'];
            $publish_date = date('Y-m-d H:i:s', strtotime($feed['publish_date']));
            $retrieve_date = date('Y-m-d H:i:s');

            $item = new stdClass();
            $item->title = $title;
            $item->link = $link;
            $item->description = $description;
            $item->publish_date = $publish_date;
            $item->retrieve_date = $retrieve_date;
            $result =  JFactory::getDbo()->insertObject('#__hotelreservation_news', $item);
        }
    }

    /**
     * get hotels object list with all the hotel ids
     * check the hotels with checkHotels helper method
     * @return string the hotelIds claimed by user
     */
    private function getHotelIds(){
        $hotelstable = JTable::getInstance('Hotels','Table');
        $hotels = $hotelstable->getAllHotelIds();
        $hotelId= array();
        $hotels = checkHotels(JFactory::getUser()->id,$hotels);
        $hotelIds="";
        foreach($hotels as $hotel){
            $hotelId[] = $hotel->hotel_id;
            $hotelIds = implode(',',$hotelId);
        }
        return $hotelIds;
    }
}
?>