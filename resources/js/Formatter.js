
/* WARNING Parameter validation is not done properly for amy of the methods.
 * This class contains methods that work on response from the server and returns relevant information
 */
export default class Formatter {

    constructor() {
        this.date_format = 'YYYY-MM-DD';
    }

    /*  Gets date, sets start date as closest previous monday (of the given date if it is a Monday),
     * sets end date as the Sunday after weeks given by @param weeks.
     *
     * @param arrival_date  - date-string
     * @param weeks         - number (default 6)
     * WARNING Error checking not done..
     */
    init(arrival_date, weeks = 6) {
        this.arrival_date = moment(arrival_date);
        this.start_date = this.arrival_date.clone().startOf('isoWeek');
        this.end_date = this.start_date.clone().add(weeks - 1, 'w').endOf('isoWeek');
    }

    /* Converts list of rooms into a dictionary of rooms searchable by room_id
     * Returns list of all days in between...
     *
     * @param data  - object; containing key 'rooms' as a list
     * @return      - object with room_id for keys and room data as values
     */
    getRoomDictionary (data) {
        let room_dict = {};
        if (data.hasOwnProperty('rooms')) {
            let room_list = data['rooms'];
            room_list.forEach(function(room) {
                if (room.hasOwnProperty('id')) {
                    room_dict[room['id']] = room;
                }
            });
        }
        return room_dict;
    }

    /* Combine information in 'rates' and 'availability' in API response, and group them by weeks
     *
     * @param data  - object; containing key 'rates' and 'availability'
     * @return      - object date=> room_info pairs grouped into weeks.
     */
    getRoomStatusByWeek (data) {
        let weekly_result = {};
        let room_status_dates_list = {};
        if (data.hasOwnProperty('rates') && data.hasOwnProperty('availability')) {
            // get list of all dates that have availability and rates for room_ids
            room_status_dates_list = this.getRoomStatusOnDates(data);
            // pass the list to get
            weekly_result = this.groupRoomStatusByWeeks(room_status_dates_list);
        }
        return weekly_result;
    }

    /* Explodes all dates given by 'availability' and 'rate' in data; then combines corresponding data into a single list.
     *
     * @param data  - object; containing key 'rates' and 'availability'
     * @return      - object date=> room_info pairs
     */
    getRoomStatusOnDates(data) {
        let availability_dates_list = {};
        if (data.hasOwnProperty('rates') && data.hasOwnProperty('availability')) {
            let availability_list = data['availability']
            let rates_list = data['rates'];
            let that = this;
            for (let i = 0; i < availability_list.length; i++ ) {
                let availability = availability_list[i];
                if (availability.hasOwnProperty('start_date') && availability.hasOwnProperty('end_date')) {
                    let avail_period = that.getDaysInPeriod(moment(availability["start_date"]), moment(availability['end_date']));
                    let room_id = availability["room_id"];
                    avail_period.forEach((avail_date) => {
                        if (!availability_dates_list.hasOwnProperty(avail_date)) {
                            availability_dates_list[avail_date] = {};
                        }
                        if (!availability_dates_list[avail_date].hasOwnProperty(room_id)) {
                            availability_dates_list[avail_date][room_id] = {};
                        }
                        availability_dates_list[avail_date][room_id]['available'] = availability['available'];
                    });
                }
            }
            rates_list.forEach((rate) => {
                if (rate.hasOwnProperty('start_date') && rate.hasOwnProperty('end_date')) {
                    let rate_period = that.getDaysInPeriod(moment(rate["start_date"]), moment(rate['end_date']));
                    let room_id = rate["room_id"];
                    rate_period.forEach((rate_date) => {
                        if (!availability_dates_list.hasOwnProperty(rate_date)) {
                            availability_dates_list[rate_date] = {};
                        }
                        if (!availability_dates_list[rate_date].hasOwnProperty(room_id)) {
                            availability_dates_list[rate_date][room_id] = {
                                'available' : false
                            };
                        }
                        availability_dates_list[rate_date][room_id]['rate'] = rate['rate'];
                    });
                }
            });
        }
        return availability_dates_list;
    }

    /* Groups given room status into weeks..
     * @param room_status_dates_list - object containing list of date=>room_info pairs
     * @return                       - Information in @param room_status_dates_list grouped into 6 weeks based on
     *                                 start_date and end_date arrived at during initialisation.
     */
    groupRoomStatusByWeeks(room_status_dates_list = {}) {
        let from_date = this.start_date;
        let to_date = this.end_date;
        let weeks = [];
        let dates_bucket = {};
        let curr_week = null;
        while(from_date.diff(to_date) < 0) {
            let new_date = from_date.clone();
            let date_string = new_date.format(this.date_format);
            let date_data = (room_status_dates_list.hasOwnProperty(date_string)) ?
                room_status_dates_list[date_string] : {};
            dates_bucket[date_string] = date_data;
            if (curr_week == null) {
                curr_week = from_date.week();
            } else if (curr_week !== from_date.week()) {
                weeks.push(dates_bucket);
                dates_bucket = {};
                curr_week = from_date.week();
            }
            from_date.add(1, 'days')
        }
        return weeks;
    }

    /* To get all days between two days
     *
     * @param start_date  - date
     * @param end_date    - date
     * @return            - list of weeks with all days (date-string) in between @param start_date and @param end_date
     */
    getDaysInPeriod (from_date, to_date) {
        let dates_list = [];
        while(from_date.diff(to_date) < 0) {
            let new_date = from_date.clone();
            dates_list.push(new_date.format('YYYY-MM-DD'));
            from_date.add(1, 'days');
        }
        return dates_list;
    }

    /* To get all days between two days grouped in weeks...
     *
     * @param start_date  - date
     * @param end_date    - date
     * @return            - list of weeks with all days in between @param start_date and @param end_date
     */
    getDaysInPeriodAsWeeks(from_date, to_date) {
        let dates = [];
        let weeks = [];
        let dates_bucket = [];
        let curr_week = null;
        while(from_date.diff(to_date) < 0) {
            let new_date = from_date.clone();
            dates_bucket.push(new_date.format('MMMM DD YYYY'));
            dates.push(new_date.format('MMMM DD YYYY'));

            if (curr_week == null) {
                curr_week = from_date.week();
            } else if (curr_week !== from_date.week()) {
                weeks.push(dates_bucket);
                dates_bucket = [];
                curr_week = from_date.week();
            }
            from_date.add(1, 'days')
        }
        return weeks;
    }
}