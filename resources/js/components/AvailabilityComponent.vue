
<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ page_title }}</h3>
                        <div class="card-tools" style="display: block;">
                            <div class="search-input">
                                <div class="search-date">
                                    <datepicker v-model="state.arrival_date" name="arrival_date"></datepicker>
                                </div>
                                <div class="search-spinner">
                                    <NumberInputSpinner
                                            :min="0"
                                            :max="10"
                                            :step="1"
                                            :integerOnly="true"
                                            v-model="state.no_of_guests"
                                    ></NumberInputSpinner>
                                </div>
                            </div> <!-- end of .search-input -->
                            <div class="search-button">
                                <button class="btn btn-success" @click="checkAvailability">
                                    Search <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div> <!-- end of .card-tools -->
                    </div> <!-- end of .card-header -->

                    <div class="card-body">
                        <div class="calendar" style="width: 70%; float: left;">
                            <div id="myCarousel" class="carousel slide" data-interval="false" data-wrap="false" data-ride="carousel">
                                <!-- Indicators -->
                                <ol class="carousel-indicators">
                                    <li data-target="#myCarousel" v-for="(item, index) in weekly_result" v-bind:data-slide-to="index" :class="{ 'active': index == 0 }"></li>
                                </ol>
                                <!-- Wrapper for slides -->
                                <div class="carousel-inner">
                                    <div v-for="(item, index) in weekly_result" class="item" :class="{ 'active': index == 0 }">
                                        <div class="calendar-row calender-heading">
                                            <span class="middle">Week #{{index + 1}}</span>
                                        </div>
                                        <div class="calendar-row" v-for="(date_rooms, date) in item">
                                            <div class="calendar-object calendar-day box date-box">
                                                <div class="day-of-week center text-size-2">
                                                    {{ date | displayDayOfWeek }}
                                                </div>
                                                <div class="day center text-size-3">
                                                    {{ date | displayDay}}
                                                </div>
                                                <div class="calendar-date center text-size-1">
                                                    {{ date | displayMonth }} {{ date | displayYear }}
                                                </div>
                                            </div> <!-- end of .calendar-object.calendar-day -->
                                            <div class="calendar-object calendar-rooms">
                                                <div v-if=" Object.keys(date_rooms).length == 0" class="no-rooms">
                                                    <span class="middle">No Rooms Available</span>
                                                </div>
                                                <div class="room box" :class="{ 'not-available': room.available != 1 }" v-for="(room, room_no) in date_rooms">
                                                    <div class="room-wrapper" v-if="room_dictionary.hasOwnProperty(room_no)">
                                                        <div class="box-title">{{ room_dictionary[room_no]['name'] }}</div>
                                                        <div class="box-content">
                                                            <span><i class="fa fa-bed"> </i> {{ room_dictionary[room_no]['bedrooms'] }}</span> |
                                                            <span><i class="fa fa-users"></i> {{ room_dictionary[room_no]['max_guests'] }}</span>
                                                        </div>
                                                        <div>
                                                            <i class="far fa-money-bill-alt"></i>
                                                            {{ booking_property.currency}} {{ (room.hasOwnProperty('rate')) ? room.rate : room_dictionary[room_no]['default_rate'] }}
                                                            ( {{ (room_dictionary[room_no]['tax_inclusive']) ? "Inc Tax" : " + Tax" }} )
                                                        </div>
                                                        <div class="box-content availability avail-yes" v-if="room.available == 1">
                                                            Available
                                                            <input type="checkbox" v-bind:name="date + room_no"
                                                                   v-if="room.available == 1"
                                                                   @change="handleCheckboxChange($event, date, room_no, (room.hasOwnProperty('rate')) ? room.rate : room_dictionary[room_no]['default_rate'], room_dictionary[room_no]['tax_inclusive'])"
                                                                   :disabled="this.bookings_available || (summary.hasOwnProperty('room_no') && summary.room_no !== -1 && summary.room_no !== room_no)">
                                                        </div>
                                                        <div class="box-content availability avail-no" v-if="room.available != 1">
                                                            Not Available
                                                        </div>
                                                    </div> <!-- end of .room-wrapper -->
                                                </div> <!-- end of .room.box -->
                                            </div> <!-- end of .calendar-object calendar-rooms -->
                                        </div> <!-- end of .calendar-row -->
                                    </div> <!-- end of .item -->
                                </div> <!-- end of .carousel-inner -->

                                <!-- Left and right controls -->
                                <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                    <span class="glyphicon glyphicon-chevron-left"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div> <!-- end of #mycarousel -->
                        </div><!-- end of .calendar -->
                        <div class="selection-summary">
                            <div v-if="bookings_available" class="summary-content">
                                <h1>Summary</h1>
                                <div>
                                    <h2>Chosen Date of Arrival</h2>
                                    <p>{{arrival_date | displayDate}}</p>
                                </div>
                                <div>
                                    <h2>No of Guests</h2>
                                    <p>{{no_of_guests}}</p>
                                </div>
                                <div>
                                    <h2>No of nights</h2>
                                    <p>{{summary.dates_picked.length}}</p>
                                    <span>
                                        (<span v-for="date in summary.dates_picked">{{date | displayDayOfWeekFull }}, {{date | displayDate }};</span>)
                                    </span>
                                </div>
                                <div v-if="summary.room_rates_with_tax !== 0">
                                    <h2>Room(s) Rate (incl taxes)</h2>
                                    <p>{{summary.room_rates_with_tax}}</p>
                                </div>
                                <div v-if="summary.room_rates_without_tax !== 0">
                                    <h2>Room(s) Rate (without taxes)</h2>
                                    <p>{{summary.room_rates_without_tax}}</p>
                                </div>
                                <div v-if="summary.room_rates_without_tax !== 0">
                                    <h2>Taxes applied</h2>
                                    <p>{{summary.tax_applied}}</p>
                                </div>
                                <div>
                                    <h2>Total Booking Cost</h2>
                                    <p>{{summary.booking_total}}</p>
                                </div>
                                <div class="center summary-buttons">
                                    <button type="button" class="btn btn-danger" @click="clearRoomSelection">
                                        Clear Selection
                                    </button> |
                                    <button type="button" class="btn btn-success" @click="newEnquiry">
                                        Enquire
                                    </button>
                                </div>
                                <div v-if="enquiry_outcome !== null">
                                    <h2>Your Enquiry</h2>
                                    <pre>{{enquiry_outcome}}</pre>
                                </div>
                            </div> <!-- end of .summary-content -->
                        </div> <!-- end of .selection-summary -->
                    </div> <!-- end of .card-body -->
                </div> <!-- end of .card -->
            </div> <!-- end of .coll-md-12 -->
        </div> <!-- end of .row -->
        <div class="modal fade" id="enquire" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewLabel">Guest Enquiry</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> <!-- end of modal-header -->
                    <form @submit.prevent="enquireNow()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="enquiry.first_name" type="text" name="first_name"
                                       placeholder="First Name"
                                       class="form-control">
                            </div>
                            <div class="form-group">
                                <input v-model="enquiry.last_name" type="text" name="last_name"
                                       placeholder="Last Name"
                                       class="form-control">
                            </div>
                            <div class="form-group">
                                <input v-model="enquiry.email" type="text" name="email"
                                       placeholder="Email"
                                       class="form-control">
                            </div>
                        </div> <!-- end of modal-body -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Enquire Now</button>
                        </div> <!-- end of modal-footer -->
                    </form>
                </div> <!-- end of modal-content -->
            </div>  <!-- end of modal-dialog -->
        </div>  <!-- end of Modal -->
    </div> <!-- end of container -->
</template>


<script>
    export default {
        data () {
            return {
                page_title : 'Enquiry calendar',
                booking_property: {},   // holds data of returned properties
                room_dictionary : {},    // object of rooms retrievable by room_id
                weekly_result: {},      // List of 6 weeks of dates and bookings from arrival_date. If arrival_date
                                        // was not a Monday, the closet previous Monday would be selected.
                selection : {},         // to track the selection. Object should contain dates pointing to the
                                        // selected room details for that date
                bookings_available: false, // set to true if  there is any selection.
                summary: {              // default value of summary
                    "booking_total" : 0,
                    "room_no" : -1,
                    "dates_picked" : [],
                    "room_rates_with_tax" : 0,
                    "room_rates_without_tax" : 0,
                    "tax_applied" : 0,
                },
                arrival_date : "",          // record of currently searched arrival date
                no_of_guests : 0,           // record of currently searched no_of_guests
                state : {                   // holds current state of search inputs
                    arrival_date : new Date(),
                    no_of_guests : 0,
                },
                enquiry_outcome : null,     // outcome of the enquiry
                enquiry : {                 // enquiry for information.
                    first_name : "",
                    last_name : "",
                    email : "",
                }
            }
        },
        mounted() {
            // on load..
            this.checkAvailability();
        },
        methods : {
            // checks if there are rooms matching current search criteria
            checkAvailability() {
                // clear current selection...
                this.clearRoomSelection();
                // then...
                axios.post('api/availability/search', {
                    "arrival_date" : this.state.arrival_date,
                    "no_of_guests" : this.state.no_of_guests,
                }).then(({ data }) => {
                    //  store current search criteria.
                    this.arrival_date = data.arrival_date;
                    this.no_of_guests = data.no_of_guests;
                    // and property information.
                    this.booking_property = data.property;
                    this.page_title = this.booking_property.name;
                    // initialse formatter..
                    this.$formatter.init(data.arrival_date);
                    // get dictionary of rooms
                    this.room_dictionary = this.$formatter.getRoomDictionary(data);
                    // and all available room data for 6 weeks...
                    this.weekly_result = this.$formatter.getRoomStatusByWeek(data);
                });
            },
            // Clear enquiry form data.. this will clear the input in the enquiry modal
            clearEnquiryFormData() {
                // reset enquiry data
                this.enquiry.first_name = "";
                this.enquiry.last_name = "";
                this.enquiry.email = "";
            },
            // to open the enquiry modal form
            newEnquiry() {
                this.clearEnquiryFormData();
                // and then show
                $("#enquire").modal('show');
            },
            // to close the enquiry modal form
            dismissEnquiryForm() {
                $("#enquire").modal('hide');
            },
            // Post an enquiry request
            enquireNow() {
                //  clear for new enquiry outcome
                this.clearEnquiryOutcomeData();
                // then post....
                axios.post('api/availability/enquire-now', {
                    "room_id" : this.summary.room_no,
                    "arrival_date": this.arrival_date,
                    "nights": this.summary.dates_picked.length,
                    "total_rate": this.summary.booking_total,
                    "first_name" : this.enquiry.first_name,
                    "last_name" : this.enquiry.last_name,
                    "email" : this.enquiry.email,
                }).then(({ data }) => {
                    this.dismissEnquiryForm();
                    this.enquiry_outcome = JSON.stringify(data, null, 4);
                })
                .catch(({ error }) => {
                    this.dismissEnquiryForm();
                    this.enquiry_outcome = "There was an error making your enquiry. Please refresh the page and try again.";
                    // console.log(error);
                });
            },
            // removes the pre tag with enquiry response json
            clearEnquiryOutcomeData() {
                this.enquiry_outcome = null;
            },
            // when changes to checkbox....
            handleCheckboxChange(e, date, room_no, rate, tax_included) {
                if ($(e.target).is(':checked')) { // add to selection if checked..
                    this.selection[date] = {
                        'room_no' : room_no,
                        'rate' : rate,
                        'tax_included' : tax_included,
                    }
                } else { // or remove
                    delete(this.selection[date]);
                }
                this.clearEnquiryOutcomeData(); // Why? maybe thee old enquiry is no longer valid once anew selection is made.
                this.compileSummary(); // compile summary based on updated selection information...
            },
            // Created summary data based on current value of this.selection....
            compileSummary() {
                /*  calculating...
                 * booking total (using tax settings, currency and tax values),
                 * dates picked
                 * number of guests
                 * If tax inclusive – include tax in rate,
                 * if tax is exclusive add onto the rate value and display it in the summary as a separate entry
                 */
                let dates_picked = [];
                let room_no = "";
                let room_rate_sum_without_tax = 0;
                let room_rate_sum_with_tax = 0;
                let room_rate_taxes_applied = 0;
                let booking_total = 0;

                for(var date in this.selection){
                    dates_picked.push(date);
                    let date_info = this.selection[date];
                    if (room_no == "") {
                        room_no = date_info.room_no;
                    }
                    if (date_info.tax_included) {
                        room_rate_sum_with_tax += date_info.rate;
                    } else {
                        room_rate_sum_without_tax += date_info.rate;
                        room_rate_taxes_applied += (date_info.rate * this.booking_property.tax) / 100;
                    }
                }
                // calculate booking total
                booking_total = room_rate_sum_without_tax + room_rate_sum_with_tax + room_rate_taxes_applied;

                if (dates_picked.length > 0) {
                    this.bookings_available = true; // even if already true...
                    this.summary = {
                        "booking_total" : booking_total,
                        "room_no" : room_no,
                        "dates_picked" : dates_picked,
                        "room_rates_with_tax" : room_rate_sum_with_tax,
                        "room_rates_without_tax" : room_rate_sum_without_tax,
                        "tax_applied" : room_rate_taxes_applied,
                    };
                } else {
                    this.clearRoomSelectionData();
                }
            },
            // to clear all checkboxes inside the calendar carousel  and the room selection data...
            clearRoomSelection() {
                $("#myCarousel input[type='checkbox']").prop('checked', false)
                this.clearRoomSelectionData();
            },
            // To clear data of room selection..
            clearRoomSelectionData() {
                this.selection = {};
                this.bookings_available = false;
                this.summary = {
                    "booking_total" : 0,
                    "room_no" : -1,
                    "dates_picked" : [],
                    "room_rates_with_tax" : 0,
                    "room_rates_without_tax" : 0,
                    "tax_applied" : 0,
                };
            }
        },
    }
</script>

<style scopped>
    pre {
        font-size: 1.5em;
    }
    .text-size-1 {
        font-size: 1em;
    }
    .text-size-2 {
        font-size: 2em;
    }
    .text-size-3 {
        font-size: 3em;
    }
    .text-size-4 {
        font-size: 4em;
    }
    .text-size-5 {
        font-size: 5em;
    }
    .text-size-6 {
        font-size: 6em;
    }

    .search-input {
        width: 80%;
        float: left;
    }
    .search-date {
        width: 50%;
        margin: 0px;
        display: block;
        float: left;
        font-size: 1.5rem;
    }
    .search-date input {
        font-size: 1.7rem;
        line-height: 35px;
        padding-left: 15px;
        width: 95%;
    }
    .search-spinner {
        width: 20%;
        display: block;
        float: left;
    }
    .search-button {
        width: 20%;
        margin: 0;
        display: block;
        float: left;
    }

    .fade {
        opacity : 1;
    }
    .calendar-row {
        min-height: 10rem;
    }
    .calendar-row.calender-heading {
        background-color: orangered;
        text-align: center;
        color: white;
        font-size: 3em;
    }

    .no-rooms {
        text-align: center;
        color: white;
        font-size: 2em;
    }
    .center {
        text-align: center;
    }
    .middle {
        height: 90px;
        line-height: 90px;
    }

    .calendar-object {
        float: left;
        min-height: 100px;
    }

    .calendar-day {
        width: 20%;
    }

    .calendar-rooms {
        width: 80%;
    }

    .box {
        width: 11em;
        height: 11em;
        padding: 1em;
        float: left;
        box-sizing: border-box;
    }
    .box .box-title {
        height: 25px;
        overflow: hidden;
        font-size: 1.5em;
    }
    .box .box-content {
        font-size: 1.2em;
    }
    .box.date-box {
        background: whitesmoke;
    }
    .box.not-available {
        background: orangered; /* always */
    }

    /* finally, alternative styling for  */
    .calendar-row:not(.calender-heading):nth-of-type(2n) {
        background-color: lightgreen;
    }
    .calendar-row:not(.calender-heading):nth-of-type(2n+1) {
        background-color: cadetblue;
    }
    .calendar-row:not(.calender-heading):nth-of-type(2n) .box {
        border-bottom: 0.5px solid lightgreen;
    }
    .calendar-row:not(.calender-heading):nth-of-type(2n+1) .box {
        border-bottom: 0.5px solid cadetblue;
    }
    .calendar-row:not(.calender-heading) .box:not(.date-box){
        color: whitesmoke;
    }
    .calendar-row:not(.calender-heading):nth-of-type(2n) .box:not(.date-box):nth-of-type(2n+1):not(.not-available) {
        background: darkslategrey;
    }
    .calendar-row:not(.calender-heading):nth-of-type(2n) .box:not(.date-box):nth-of-type(2n):not(.not-available) {
        background: dimgray;
    }
    .calendar-row:not(.calender-heading):nth-of-type(2n+1) .box:not(.date-box):nth-of-type(2n+1):not(.not-available) {
        background: blueviolet;
    }
    .calendar-row:not(.calender-heading):nth-of-type(2n+1) .box:not(.date-box):nth-of-type(2n):not(.not-available) {
        background: greenyellow;
    }

    .selection-summary {
        width: 30%;
        float: left;
        padding: 1em 2em;
    }
    .summary-content {
        border: 1px solid black;
        border-radius: 5px;
    }
    .summary-content h1 {
        padding: 1em 0.5em;
    }
    .summary-content div h2 {
        background: gray;
        color: white;
        padding: 0.5em;
    }
    .summary-content div p {
        padding: 0.5em 1em 0;
        font-size: 1.4em;
    }
    .summary-content div span {
        padding: 0.5em 1em 0;
        font-size: 1.2em;
    }
    .summary-buttons {
        margin-bottom: 1em;
    }

    .availability {
        vertical-align: top;
        min-height: 20px;
    }

    .btn {
        font-size: 2em;
        height: 36px;
    }
    .carousel-control {
        height: 10%;
        width: 10%;
        background-image: unset !important;
    }
    .carousel-indicators {
        bottom: unset;
        top: 0.5em;
        left: 3em;
    }
    .carousel-indicators li {
        margin-top: 4px;
    }
</style>