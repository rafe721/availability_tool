export default class Formatter {

    constructor() {
        this.date_format = 'YYYY-MM-DD';
    }

    init(arrival_date = "") {
        this.arrival_date = moment(arrival_date);
        this.start_date = this.arrival_date.clone().startOf('isoWeek');
        this.end_date = this.start_date.clone().add(5, 'w').endOf('isoWeek');
        // console.log(this.arrival_date.format('MMMM DD YYYY'));
        // console.log(this.start_date.format('MMMM DD YYYY'));
        // console.log(this.end_date.format('MMMM DD YYYY'));
        // this.getDaysBetween(this.start_date, this.end_date);
    }

    getDaysBetween(from_date, to_date) {
        let dates = [];
        let weeks = [];
        let dates_bucket = [];
        let curr_week = null;
        while(from_date.diff(to_date) < 0) {
            // console.log(from_date.toDate());
            // console.log(from_date.week());
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
        // console.log(weeks);
        return weeks;
    }

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

    getRoomStatusByWeek (data) {
        let weekly_result = {};
        let room_status_dates_list = {};
        if (data.hasOwnProperty('rates') && data.hasOwnProperty('availability')) {
            room_status_dates_list = this.getRoomStatusOnDates(data);
            // console.log(room_status_dates_list);
            weekly_result = this.groupRoomStatusByWeeks(room_status_dates_list);
            // console.log(weekly_result);
        }
        return weekly_result;
    }

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

    // returns a list of
    getRoomStatusOnDates(data) {
        let availability_dates_list = {};
        if (data.hasOwnProperty('rates') && data.hasOwnProperty('availability')) {
            let availability_list = data['availability']
            let rates_list = data['rates'];
            let that = this;
            for (let i = 0; i < availability_list.length; i++ ) {
                let availability = availability_list[i];
                if (availability.hasOwnProperty('start_date') && availability.hasOwnProperty('end_date')) {
                    let avail_period = that.getPeriod(moment(availability["start_date"]), moment(availability['end_date']));
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
                    let rate_period = that.getPeriod(moment(rate["start_date"]), moment(rate['end_date']));
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
        // console.log(availability_dates_list);
        return availability_dates_list;
    }

    getPeriod (from_date, to_date) {
        let dates_list = [];
        while(from_date.diff(to_date) < 0) {
            let new_date = from_date.clone();
            dates_list.push(new_date.format('YYYY-MM-DD'));
            from_date.add(1, 'days');
        }
        // console.log(dates_list);
        return dates_list;
    }
}