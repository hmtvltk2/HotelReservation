/**
 * Created by enix on 08/05/16.
 */
//document.body.innerHTML = greeter(user);
var JHotelUtils = (function () {
    function JHotelUtils() {
    }
    /**
     *
     */
    //constructor() {
    //    if (JHotelUtils.instance) {
    //        throw new Error("Init failed :  Use JHotelUtils.getInstance() instead of new");
    //    }
    //    JHotelUtils.instance = this;
    //}
    JHotelUtils.getInstance = function () {
        if (this.instance === null || this.instance === undefined) {
            this.instance = new JHotelUtils();
        }
        else {
            alert("Only one instance of the this singleton is allowed");
        }
        return this.instance;
    };
    JHotelUtils.greeter = function (person) {
        return "Hello, " + person.firstName + " " + person.lastName;
    };
    return JHotelUtils;
})();
//# sourceMappingURL=utils.js.map