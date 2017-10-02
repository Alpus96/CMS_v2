class Cookies {
    /**
    *       @desc		Constructor does the required setup on initiation. Sets
    *                   standard duration for cookies and caches current cookies.
    *
    *       @param   	duration; <Number>, the standad duration a cookie should exist.
    *
    *       @throws  	If the parameter duration was not passed; "Duration required;
    *                   The instance of Cookies requires a standard cookie duration in
    *                   milliseconds. [ new Cookies(milliseconds) ]".
    **/
    constructor(duration) {
        //  Confirm a standard cookie duration was passed and save it.
        this.duration = this.assertDuration(duration);
        //  Finish initiation by caching pre-existing cookies.
        this.read('');
    }

    /**
    *       @desc       This function asserts that a valid duration was passed.
    *
    *       @param   	duration; Any, the duration parameter that should be asserted.
    *
    *       @returns 	Returns the duration of it was valid or parseable.
    *
    *       @throws  	If the duration was not a number and can not be parsed to a number;
    *                   "Invalid duration; The duration should be in Number of milliseconds.
    *                   (Is ' + typeof duration + ')"
    **/
    assertDuration (duration) {
        //  Check if the duration is not a number.
        if (typeof duration != 'number') {
            //  If not try parsing it.
            const parsed = !isNaN(duration) ? parseInt(duration) : false;
            //  If unable to parse throw an error.
            if (parsed === false) { throw new Error('Invalid duration; The duration should be in Number of milliseconds. (Is ' + typeof duration + ')'); }
            //  Else return the parsed duration.
            return parsed;
        }
        //  If the duration was a number return it.
        return duration;
    }

    /**
    *       @desc       Converts milliseconds to string of time after milliseconds.
    *
    *       @param    	duration; <Number>, in how long from now I want the date string for.
    *
    *       @return    	Returns an object with string and milliseconds of current time + duration.
    *
    *       @throws   	If the duration was not a number; "Invalid duration; The duration
    *                   should be in Number of milliseconds. (Is ' + typeof duration + ')".
    **/
    expireStamp (duration = this.duration) {
        //  Confirm a valid duration was passed.
        duration = this.assertDuration(duration);
        //  Set time to after duration.
        const date = new Date();
        date.setTime(date.getTime() + duration + (-1*date.getTimezoneOffset()*60*1000));
        //  Return the string of time after duration.
        return date.toUTCString();
    }

    /**
    *       @desc		This function checks if it a cookie has expired, if not
    *               	checks again when it would.
    *
    *       @param		name; <String>, the name of the cookie to check.
    **/
    expired (name) {
        //  Check that the cookie has not been deleted manualy, return if it has.
        if (typeof this.cache[name] === 'undefined') { return; }
        //  If not delete it.
        delete this.cache[name];
    }

    /**
    *       @desc       Creates a cookie.
    *
    *       @param      name; <String>, the name of the cookie that is created.
    *                   value: Any, a variable that is conterted to a JSON string and saved in the cookie.
    *                   exp; <Number>, the amount of milliseconds the cookie will exist.
    **/
    create (name, value, duration = this.duration) {
        //  Confirm a valid duration was passed.
        duration = this.assertDuration(duration);
        //  Get the millis and string of when the cookie expires.
        const expires = setTimeout( () => { this.expired(name); }, duration );
        //  Create the new cookie.
        document.cookie = name + '=' + JSON.stringify( value ? { value: value, expires: expires } : '' ) + '; expires=' + this.expireStamp(duration) + '; path=/';
        //  Add the new cookie to the cache.
        this.cache[name] = { value: value, expires:  expires};
    }

    /**
    *       @desc       Returns a cookie with the passed name if it has been cached. If it
    *                   does not all current cookies are cached and the cookie with the
    *                   passed is returned if it exists.
    *
    *       @param   	name; <String>, the name of the cookie to read.
    *
    *       @return   	An object with the cookie value.
    **/
    read (name) {
        //  If the cookie is already cached return it.
        if (this.cache && typeof this.cache[name] !== 'undefined' && typeof this.cache[name].expires !== 'undefined') { return this.cache[name].value; }
        //  Otherwise cache all current cookies.
        const cookies = document.cookie.split('; ');
        this.cache = {};
        for (let i = 0; i < cookies.length; i++) {
            //  Only split the name and the value for the first occurance of '='.
            const cookieName = cookies[i].substr(0, cookies[i].indexOf('='));
            const cookieJSON = cookies[i].substr(cookies[i].indexOf('=')+1);
            //  Decode the JSON string.
            const cookieValue = typeof cookieJSON !== 'undefined' && cookieJSON !== 'undefined' && cookieJSON ? JSON.parse(cookieJSON) : undefined;
            this.cache[cookieName] = cookieValue;
        }
        //  Then return the cookie if it exists, otherwise return null.
        return this.cache[name] ? this.cache[name].value : null;
    }

    /**
    *		@desc       Updates the expiration stamp for the cookie with the passed name.
    *
    *   	@param		name; <String>, the name of the cookie to update.
    *               	duration; <Number>, how long from the current time the cookie expires in milliseconds.
    *
    *   	@throws  	If a cokkie with the passed name does not exist; "Can not set expires of undefined.".
    **/
    extendDuration (name, duration) {
        //  Confirm a valid duration was passed.
        duration = this.assertDuration(duration);
        //  Check that the cookie exists before extending duration, if it does not exist return.
        if (typeof this.cache[name] === 'undefined') { throw new Error('Can not extend duration of undefined.'); }
        //  If it still exists get it.
        const oldCookie = this.cache[name];
        //  Remove the timeout.
        clearTimeout(oldCookie.expires);
        //  Create the cookie again to overwrite the old one.
        this.create(name, oldCookie.value, duration ? duration : this.duration);
    }

    /**
    *       @desc       Deletes the cookie with the passed name.
    *
    *       @param    	name; <String>, the name of the cookie to delete.
    **/
    delete (name) {
        //  Check that the cookie exists, otherwise return.
        if (typeof this.cache[name] === 'undefined') { return; }
        //  Create the cookie with an empty value and expiration 1 millisecond ago.
        this.create(name, '', -1);
        //   Then remove it from the cache.
        delete this.cache[name];
    }

}

const cookie = new Cookies(10*60*1000);