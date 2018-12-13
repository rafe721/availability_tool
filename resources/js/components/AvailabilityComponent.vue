
<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ page_title }}</h3>

                        <div class="card-tools" style="display: block;">
                            <div class="search-input" style="width: 80%; float: left">
                                <div class="search-date" style="width: 40%; margin: 0;display: block;float: left;">
                                    <datepicker v-model="state.arrival_date" name="arrival_date"></datepicker>
                                </div>
                                <div class="search-spinner" style="width: 50%;display: block;float: left;">
                                    <NumberInputSpinner
                                            :min="0"
                                            :max="10"
                                            :step="1"
                                            :integerOnly="true"
                                            v-model="state.no_of_guests"
                                    ></NumberInputSpinner>
                                </div>
                            </div>
                            <div class="search-button" style="width: 20%; margin: 0;display: block; float: left;">
                                <button class="btn btn-success" @click="checkAvailability">
                                    Search <i class="fas fa-user-plus fa-fw"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="calendar" style="width: 60%; float: left;">
                            <div id="myCarousel" class="carousel slide" data-interval="false" data-wrap="false" data-ride="carousel">
                                <!-- Indicators -->
                                <ol class="carousel-indicators">
                                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                                    <li data-target="#myCarousel" data-slide-to="1"></li>
                                    <li data-target="#myCarousel" data-slide-to="2"></li>
                                </ol>

                                <!-- Wrapper for slides -->
                                <div class="carousel-inner">
                                    <div v-for="(item, index) in weekly_result" class="item" :class="{ 'active': index == 0 }">
                                        Week {{ index + 1 }}
                                        <div v-for="(date_rooms, date) in item" style="width: 60%; margin: 10% 20%;">
                                            <div class="calendar-date">
                                                {{ date | displayDate}}
                                            </div>
                                            <div class="calendar-rooms">
                                                <div class="room" v-for="(room, room_no) in date_rooms">
                                                    {{ room_no }}
                                                    <div v-if="room_dictionary.hasOwnProperty(room_no)">
                                                        <p>{{ room_dictionary[room_no]['name'] }}</p>
                                                        <p>Bedrooms: {{ room_dictionary[room_no]['bedrooms'] }}</p>
                                                        <p>Maximum guests{{ room_dictionary[room_no]['max_guests'] }}</p>
                                                        <p>
                                                            NZD {{ (room.hasOwnProperty('rate')) ? room.rate : room_dictionary[room_no]['default_rate'] }}
                                                            ( {{ (room_dictionary[room_no]['tax_inclusive']) ? "Includes Taxes" : "taxes Extra" }} )
                                                        </p>
                                                        <input type="checkbox"
                                                               v-if="room.available == 1"
                                                               @change="handleChange($event, date, room_no, (room.hasOwnProperty('rate')) ? room.rate : room_dictionary[room_no]['default_rate'], room_dictionary[room_no]['tax_inclusive'])"
                                                               :disabled="this.bookings_available || (summary.hasOwnProperty('room_no') && summary.room_no !== -1 && summary.room_no !== room_no)">
                                                        <div v-if="room.available == 1">
                                                            Available
                                                        </div>
                                                        <div v-if="room.available != 1">
                                                            Not Available
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Left and right controls -->
                                <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                    <span class="glyphicon glyphicon-chevron-left"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div><!-- end of 'calendar class' -->
                        <div class="selection-summary" style="width: 40%; float: left;">
                            <div v-if="bookings_available">
                                <h1>Summary</h1>
                                <div>
                                    Arrival Date : {{arrival_date}}
                                </div>
                                <div>
                                    No of Guests : {{no_of_guests}}
                                </div>
                                <div>
                                    No of nights : {{summary.dates_picked.length}}
                                </div>
                                <div>
                                    Room Rate (incl taxes) : {{summary.room_rates_with_tax}}
                                </div>
                                <div>
                                    Room Rate (without taxes) : {{summary.room_rates_without_tax}}
                                </div>
                                <div>
                                    Taxes applied : {{summary.tax_applied}}
                                </div>
                                <div>
                                    Total Booking Cost : {{summary.booking_total}}
                                </div>
                                <button class="btn btn-success" @click="newEnquiry">
                                    Enquire <i class="fas fa-user-plus fa-fw"></i>
                                </button>
                                <div v-if="enquiry_outcome !== null">
                                    Enquiry outcome
                                    {{enquiry_outcome}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="enquire" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewLabel">Guest Enquiry</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Enquire Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        data () {
            return {
                page_title : 'Enquiry calendar',
                room_dictionary : {},
                weekly_result: {},
                selection : {},
                summary: {
                    "booking_total" : 0,
                    "room_no" : -1,
                    "dates_picked" : [],
                    "room_rates_with_tax" : 0,
                    "room_rates_without_tax" : 0,
                    "tax_applied" : 0,
                },
                booking_property: {},
                bookings_available: false,
                arrival_date : "",
                arrival_date : 0,
                state : {
                    arrival_date : new Date(),
                    no_of_guests : 0,
                },
                enquiry_outcome : null,
                search : new Form({
                    arrival_date : '',
                    no_of_guests : '',
                }),
                enquiry : {
                    first_name : "",
                    last_name : "",
                    email : "",
                }
            }
        },
        mounted() {
            this.checkAvailability();
        },
        methods : {
            checkAvailability() {
                // console.log("Searched");
                // console.log(this.search);
                axios.post('api/availability/search', {
                    "arrival_date" : this.state.arrival_date,
                    "no_of_guests" : this.state.no_of_guests,
                }).then(({ data }) => {
                    console.log(data);
                    // var arrival_date = moment(data.arrival_date);
                    // var arrival_start_of_week = arrival_date.clone().startOf('isoWeek');
                    // var end_date = arrival_start_of_week.clone().add(4, 'w').endOf('isoWeek');
                    // console.log(arrival_date.format('MMMM DD YYYY'));
                    // console.log(arrival_start_of_week.format('MMMM DD YYYY'));
                    // console.log(end_date.format('MMMM DD YYYY'));
                    this.arrival_date = data.arrival_date;
                    this.no_of_guests = data.no_of_guests;
                    this.$formatter.init(data.arrival_date);
                    this.booking_property = data.property;
                    this.page_title = this.booking_property.name;
                    this.room_dictionary = this.$formatter.getRoomDictionary(data);
                    this.weekly_result = this.$formatter.getRoomStatusByWeek(data);

                    console.log(this.room_dictionary);
                    console.log(this.weekly_result);

                    // console.log(this.$formatter.getDaysBetween(this.$formatter.start_date, this.$formatter.end_date));
                });
            },
            newEnquiry() {
                // this.enquiry.reset();
                $("#enquire").modal('show');
            },
            dismissEnquiryForm() {
                // this.enquiry.reset();
                $("#enquire").modal('hide');
            },
            enquireNow() {
                this.enquiry_outcome == null;
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
                    this.enquiry_outcome = data;
                    console.log(data)
                })
                .catch(({ error }) => {
                    this.dismissEnquiryForm();
                    this.enquiry_outcome = "There was an error making your enquiry. Please refresh the page and try again.";
                    console.log(error)
                });
            },
            handleChange(e, date, room_no, rate, tax_included) {
                // console.log(date);
                // console.log(room_no);
                // console.log(rate);
                // console.log(tax_included);
                if ($(e.target).is(':checked')) {
                    console.log('adding to selection');
                    this.selection[date] = {
                        'room_no' : room_no,
                        'rate' : rate,
                        'tax_included' : tax_included,
                    }
                } else {
                    console.log('removing from selection');
                    delete(this.selection[date]);
                }
                this.compileSummary();
            },
            compileSummary() {
                /*
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
                        console.log(this.booking_property);
                        room_rate_taxes_applied += (date_info.rate * this.booking_property.tax)/100;
                    }
                    // console.log(this.selection[date]);
                }
                booking_total = room_rate_sum_without_tax + room_rate_sum_with_tax + room_rate_taxes_applied;

                this.bookings_available = (dates_picked.length > 0) ? true : false;

                this.summary = {
                    "booking_total" : booking_total,
                    "room_no" : room_no,
                    "dates_picked" : dates_picked,
                    "room_rates_with_tax" : room_rate_sum_with_tax,
                    "room_rates_without_tax" : room_rate_sum_without_tax,
                    "tax_applied" : room_rate_taxes_applied,
                };
                console.log(this.summary);
            }
        },
    }
</script>
<style scopped>
    .search-date {
        font-size: 1.5rem;
    }
.search-date input {
    font-size: 1.7rem;
    line-height: 35px;
    padding-left: 15px;
    width: 100%;
}
.fade {
    opacity : 1;
}

</style>