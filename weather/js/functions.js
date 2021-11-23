/* date.js - Custom Date methods
 * addDays
 * addWeeks
 * addYears
 * addMonths
 * addMonthsTrunc
 * copy
 * getDaysBetween
 * getCDay
 * getDayName
 * getDayOfYear
 * getCMonth
 * getMonthName
 * getWeekDays
 * addWeekDays
 * getMonthsBetween
 * getYearsBetween
 * getAge
 * lastDay
 * sameDayEachWeek
 * to12HourString
 * to24HourString
 */

Date.prototype.DAYNAMES = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
Date.prototype.MONTHNAMES = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
Date.prototype.msPERMIN = 1000 * 60;
Date.prototype.msPERDAY = 1000 * 60 * 60 * 24;

Date.prototype.addWeekDays = function(d) {
    /* Adds the necessary number of days
     * to the date to include the required
     * weekdays.
     */
    var startDay = this.getDay();    //day of week 0 through 6
    var wkEnd = 0;              //number of weekends needed
    var m = d % 5;              //number of weekdays for partial week

    if (d < 0) {
        wkEnd = Math.ceil(d/5); //Yields a negative number of weekends

        switch (startDay) {
        case 6: 			//Staring day Sat. 1 less weekend
            if (m == 0 && wkEnd < 0) 
				wkEnd++;
            break;
        case 0:
            if (m == 0) 
				d++;        //decrease - part of weekend
            else 
				d--;        //increase - part of weekend
            break;
        default:
            if (m <= -startDay) 
				wkEnd--;	//add weekend if not enough days to cover
        }
    }
    else if (d > 0) {
        wkEnd = Math.floor(d/5);

        switch (startDay) {
        case 6:
            if (m == 0) 
				d--;
            else 
				d++;
            break;
        case 0:
            if (m == 0 && wkEnd > 0) 
				wkEnd--;
            break;
        default:
            if (5 - startDay < m) 
				wkEnd++;
        }
    }

    d += wkEnd * 2; //Add weekends to weekdays needed

    this.addDays(d);
};
Date.prototype.addDays = function(d) {
        /* Adds the number of days to the date */
        this.setDate( this.getDate() + d );
    };
Date.prototype.addWeeks = function(w) {
        /* Adds the number of weeks to the date */
        this.setDate(this.addDays(w * 7));
    };
Date.prototype.addMonths = function(m) {
        /* Adds the number of months to date
         * If starting with a day that is
         * greater than the number of days
         * in the new month the new date is
         * in the next month
         */
        this.setMonth(this.getMonth() + m);
    };
Date.prototype.addMonthsTrunc = function(m) {
        /* Adds the number of months to date,
         * but unlike addMonths this method
         * truncates extra days at end of month
         * when the start day of month is greater
         * than the number of days in the target
         * ending month (js overflows into the following month)
         */
         var d = this.getDate();
         this.setMonth(this.getMonth() + m);

         /* Adjust when original month has more days then new month and overflows */
         if (this.getDate() < d)
             this.setDate(0);
    };
Date.prototype.addYears = function(y) {
        /* Adds the number of years to date
         * If adding to 2/29 and result is not
         * a leap year the day is set to 28
         */
        var m = this.getMonth();
        this.setFullYear(this.getFullYear() + y);

        //Adjust for leap years
        if (m < this.getMonth()) {
            this.setDate(0);
        }
    };
Date.prototype.copy = function () {
        /* Creates a copy of date by value
         * Normal assignment only creates
         * a reference to the date, whereas
         * this creates a new date object
         */
        return new Date(this.getTime());
    };
Date.prototype.getDaysBetween = function(d) {
        /*Returns number of days between two dates
        * including the last day, but not the first.
        *
        * This is value can be added
        * to one date get the other date.
        */
		var tmp = d.copy();
		tmp.setUTCHours(this.getUTCHours(), this.getUTCMinutes(), this.getUTCSeconds(), this.getUTCMilliseconds());		

		var time = tmp.getTime() - this.getTime();
		return time/this.msPERDAY;
    };
Date.prototype.getCDay = function() {
        //Returns first 3 letters in day name
        return this.getDayName().slice(0,3);
    };
Date.prototype.getDayName = function() {
        //Returns full name of day
        return this.DAYNAMES[this.getDay()];
    };
Date.prototype.getDayOfYear = function() {
        //Returns day of year 1 through 365 or 366
        var start = new Date(this.getFullYear(), 0, 0);
        return this.getDaysBetween(start) * -1;
    };
Date.prototype.getCMonth = function() {
        //Returns first 3 letters in month name
        return this.getMonthName().slice(0, 3);
    }
Date.prototype.getMonthName = function() {
        //Returns full name of month
        return this.MONTHNAMES[this.getMonth()];
    };
Date.prototype.getWeekDays = function(d) {
        var wkEnds = 0, days = 0;
        var s = 0, e = 0;

        days = Math.abs(this.getDaysBetween(d));

        if (days) {
            s = (d < this) ? d.getDay() : this.getDay() ;
            e = (d < this) ? this.getDay() : d.getDay();

            wkEnds = Math.floor(days/7);

            if (s != 6 && s > e) 
				wkEnds++;
            if (s != e && (s == 6 || e == 6) ) 
				days--;

            days -= (wkEnds * 2);
        }
        return days;
    };
Date.prototype.getMonthsBetween = function(d) {
        //returns months between dates
        var d1 = this.getFullYear() * 12 + this.getMonth();
        var d2 = d.getFullYear() * 12 + d.getMonth();
        return d2 - d1;
    };
Date.prototype.getMonthsBetween2 = function(d) {
	var sDate, eDate;   
	var d1 = this.getFullYear() * 12 + this.getMonth();
	var d2 = d.getFullYear() * 12 + d.getMonth();
	var sign;
	var months = 0;
	var sDay, sLastDay, sAdj, eDay, eLast, eAdj, adj;

	if (this == d) {
		months = 0;
	} else if (d1 == d2) { //same year and month
		months = (d.getDate() - this.getDate())/this.lastday();
	} else {
		if (d1 <  d2) {
			sDate = this;
			eDate = d;
			sign = 1;
		} else {
			sDate = d;
			eDate = this;
			sign = -1;
		}

		sDay = sDate.getDate();
		sLastDay = sDate.lastday();
		eDay = eDate.getDate();
		eLastDay = eDate.lastday();

		if (sDay > eLastDay) {
			adj = eDay/eLastDay;
		} else {
			sAdj = sLastDay - sDay;
			eAdj = eDay > sLastDay ? sLastDay : eDay;
			adj = (sAdj + eAdj)/sLastDay;
		}
		months = Math.abs(d2 - d1) + (adj -1);

		if (months % 1 != 0 )
			months = (months * sign).toFixed(2);
		else
			months = (months * sign);
	}
	return months;
}
Date.prototype.getYearsBetween = function(d) {
        //returns years and fraction between dates
        var months = this.getMonthsBetween(d);
        return months/12;
    };
Date.prototype.getAge = function(d) {
		if (!d)
        	d = new Date();

        return this.getYearsBetween(d).toFixed(2);
    };
Date.prototype.lastday = function() {
        //returns number of days in month
        var d = new Date(this.getFullYear(), this.getMonth() + 1, 0);
        return d.getDate();
    };
Date.prototype.sameDayEachWeek = function (d, date) {
        /* Returns array of dates of same day each week in range */
        var aDays = new Array();
        var eDate, nextDate, adj;

        if (this > date) {
            eDate = this;
            nextDate = new Date(date.getTime());
        } else {
            eDate = date;
            nextDate = new Date(this.getTime());
        }

        adj = (d - nextDate.getDay() + 7) %7;
        nextDate.setDate(nextDate.getDate() + adj);

        while (nextDate < eDate) {
            aDays[aDays.length] = new Date(nextDate.getTime() );
            nextDate.setDate(nextDate.getDate() + 7);
        }
        return aDays;
    };
Date.prototype.to12HourString = function () {
    var h = this.getHours();
    var m = "0" + this.getMinutes();
    var s = "0" + this.getSeconds();

    var ap = "am";

    if (h >= 12) {
        ap = "pm";

        if (h >= 13)
            h -= 12;

    } else if (h == 0) {
        h = 12;
	}

    h = "0" + h;
    return h.slice(-2) + ":" + 
           m.slice(-2) + ":" + 
           s.slice(-2) + " " + ap;
}
Date.prototype.to24hourString = function () {
	var h = "0" + this.getHours();
    var m = "0" + this.getMinutes();
    var s = "0" + this.getSeconds();
    return h.slice(-2) + ":" + m.slice(-2) + ":" + s.slice(-2);
}

/* Add built-in method to earlier browsers*/
if (!Date.prototype.toDateString) {
    Date.prototype.toDateString = function() {
            return this.toString().replace(/\d\d:\d\d:\d\d\s\w{2,3}\s/, '');
        };
}
if (!Date.prototype.toLocaleDateString) {
    Date.prototype.toLocaleDateString = function() {
            return (this.getMonth() + 1) + "/" + this.getDate() + "/" + this.getFullYear();
        };
}

Date.createFromMysql = function(mysql_string)
{ 
   if(typeof mysql_string === 'string')
   {
      var t = mysql_string.split(/[- :]/);

      //when t[3], t[4] and t[5] are missing they defaults to zero
      return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);          
   }

   return null;   
}
Date.prototype.getWeek = function() { 
        var a, b, c, d, e, f, g, n, s, w; 
         
        $y = this.getFullYear(); 
        $m = this.getMonth() + 1; 
        $d = this.getDate(); 
 
        if ($m <= 2) { 
            a = $y - 1; 
            b = (a / 4 | 0) - (a / 100 | 0) + (a / 400 | 0); 
            c = ((a - 1) / 4 | 0) - ((a - 1) / 100 | 0) + ((a - 1) / 400 | 0); 
            s = b - c; 
            e = 0; 
            f = $d - 1 + (31 * ($m - 1)); 
        } else { 
            a = $y; 
            b = (a / 4 | 0) - (a / 100 | 0) + (a / 400 | 0); 
            c = ((a - 1) / 4 | 0) - ((a - 1) / 100 | 0) + ((a - 1) / 400 | 0); 
            s = b - c; 
            e = s + 1; 
            f = $d + ((153 * ($m - 3) + 2) / 5) + 58 + s; 
        } 
         
        g = (a + b) % 7; 
        d = (f + g - e) % 7; 
        n = (f + 3 - d) | 0; 
 
        if (n < 0) { 
            w = 53 - ((g - s) / 5 | 0); 
        } else if (n > 364 + s) { 
            w = 1; 
        } else { 
            w = (n / 7 | 0) + 1; 
        } 
         
        $y = $m = $d = null; 
         
        return w; 
    };
Date.prototype.getWeekOfMonth = function() {
    var month = this.getMonth()
      , firstWeekday = new Date(this.getFullYear(), month, 1).getDay()
      , offsetDate = this.getDate() + firstWeekday - 1
      , week = 1 + Math.floor(offsetDate / 7) // remove the 1 + if you want to be 0-based
      , weekInMilliseconds = 7 * 24 * 60 * 60 * 1000
    ;
    return week === 4 ? week + (month !== new Date(this.getTime() + weekInMilliseconds).getMonth()) : week;
};