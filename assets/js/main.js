$( document ).ready(function() {
    $(".form-datepicker").datepicker();

    $('input[type=radio][name=flight_type]').change(function() {
      $("#return-date").toggle();
    });
});


