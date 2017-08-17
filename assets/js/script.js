$(document).ready(function () {
    var tweet_owl = $(".tweet-carousel");
    tweet_owl.owlCarousel({items: 1, dots: true});

    $(".tweet-nav-right").click(function () {
        tweet_owl.trigger('next.owl.carousel');
    });
    $(".tweet-nav-left").click(function () {
        tweet_owl.trigger('prev.owl.carousel');
    });

    $(".followers-name").click(function () {
        var name = $(this).attr("data-name");

        $("#home-slider-name").html($(this).text() + "'s Tweets");
        $('html, body').animate({
            scrollTop: $("#home-slider").offset().top
        }, 500);
        $.ajax({
            url: "user-timeline",
            method: "GET",
            data: {screen_name: name},
            cache: false,
        }).done(function (data) {
            $(".tweet-carousel").html(data);
            tweet_owl.trigger('destroy.owl.carousel');
            tweet_owl.owlCarousel({items: 1, dots: true});
            tweet_owl.trigger('refresh.owl.carousel');
        });

        return false;
    });
    var dat = [];
     $.ajax({
            url: "get-followers-list",
            method: "GET",
            cache: false,
        }).done(function (d) {
         //  alert(d);
           dat = d;
           $('#followers-list').suggest('@', {
            data: dat,
            map: function(user) {
                alert(user);
              return {
                value: user.value,
                text: '<strong>'+user.value+'</strong> <small>'+user.label+'</small>'
              }
            }
          })
        });
       
    function sug(){
        
    }
    
    
    var cache = {};
  /*  $("#followers-list").autocomplete({
        minLength: 1,
        autoFocus: true,
        source: function (request, response) {
            var term = request.term;
            if (term in cache) {
                response({label:cache[ term ], value:cache[ term ]});
                return;
            }

            $.getJSON("get-followers-list?term="+request.term, request, function (data, status, xhr) {
              //  alert(data + "hello");
                // response( data );
                
                response($.map(data, function (item,i) {
                      //  alert(JSON.stringify(item));
                    cache[ term ] = item.label;
                    return {
                        label: item.label,
                        value: item.value
                    };
                }));

            });
        }
    });
*/
});