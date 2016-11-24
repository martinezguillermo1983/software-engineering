$( document ).ready(function() {
    $(".form-datepicker").datepicker();

    $('input[type=radio][name=flight_type]').change(function() {
      $("#return-date").toggle();
    });


    $("#city-select").select2({
      ajax: {
        url: '/software-engineering-master/city/search',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            city_name: params.term
          };
        },
        processResults: function (data, params) {
          options = []
          $.each(data, function (i, val) {
            options.push({
                id: val.id,
                text: val.name + ' (' + val.province_state.name + ', ' +  val.province_state.country.name + ')'
              }
            );
          });
          console.log(options)
          return {
            results: options,
          };
        },
        cache: true
      },
      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
    });
});


