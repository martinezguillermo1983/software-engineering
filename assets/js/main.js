$( document ).ready(function() {
    $(".form-datepicker").datepicker();

    $('input[type=radio][name=flight_type]').change(function() {
      $("#return-date").toggle();
    });


    // $('.chosen-select').chosen();
    // $('.search-city .search-field input').keydown(function(event) {     
    //    $.ajax('/software-engineering-master/city/search?city_name='+$(this).val(), {
    //       success: function(data) {
    //         currentSearch = $('.search-city .search-field input').val()
    //         cities = JSON.parse(data)
    //         $('#city-select').find('option').remove().end()  
    //         $.each(cities, function(key, value) {
    //           $('#city-select')
    //             .append($("<option></option>")
    //                       .attr("value",value.id)
    //                       .text(value.name));
    //           $(".chosen-select").trigger("chosen:updated");
    //           $('.search-city .search-field input').val(currentSearch)
    //         });
    //       },
    //       error: function(error) {
    //          alert(error)
    //          console.log(error)
    //       }
    //    });
    // });

    // $("#city-select").ajaxChosen({
    //   method: 'GET',
    //   url: '/software-engineering-master/city/search?city_name='+$('.search-city .search-field input').val(),
    //   // dataType: 'json'
    // }, function (data) {
    //   alert('test');
    //   cities = JSON.parse(data)

    //   $.each(cities, function (i, val) {
    //     terms[val.id] = item.name + ', ' + item.province_state.name + ', ' +  item.province_state.country.name;
    //   });

    //   return terms;
    // });

    // $('.search-city .chosen-choices input').autocomplete({
    //   source: function( request, response ) {
    //     $.ajax({
    //       url: '/software-engineering-master/city/search?city_name='+$('.search-city .search-field input').val(),
    //       // dataType: "json",
    //       beforeSend: function(){$('ul.chosen-results').empty();},
    //       success: function( data ) {
    //         cities = JSON.parse(data)
    //         response( $.map( cities, function( item ) {
    //           currentSearch = $('.search-city .search-field input').val()
    //           $('#city-select')
    //             .append($("<option></option>")
    //                       .attr("value",item.name)
    //                       .text(item.name));
    //           $(".chosen-select").trigger("chosen:updated");
    //           $('ul.chosen-results').append('<li class="active-result">' + item.name + ', ' + item.province_state.name + ', ' +  item.province_state.country.name + '</li>');
    //         }));
    //       }
    //     });
    //   }
    // });
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


