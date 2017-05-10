/**
 * Created by enix on 08/05/16.
 */

    interface Person {
        firstName : String;
        lastName : String;
        test:   String;
    }

//document.body.innerHTML = greeter(user);


    class JHotelUtils {

        private static instance:JHotelUtils; // = new JHotelUtils();

        constructor(){}
        /**
         *
         */
        //constructor() {
        //    if (JHotelUtils.instance) {
        //        throw new Error("Init failed :  Use JHotelUtils.getInstance() instead of new");
        //    }
        //    JHotelUtils.instance = this;
        //}

        public static getInstance() {
            if(this.instance === null || this.instance === undefined){
                this.instance = new JHotelUtils();
            }else{
                alert("Only one instance of the this singleton is allowed");
            }
            return this.instance;
        }

        public static greeter(person: Person) : String {
            return "Hello, " + person.firstName + " " + person.lastName;
        }

        //userTyping = function () {
        //    confirm("Type your name : ");
        //}

    }