<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>

    <title>Hello, world!</title>
    <style>
    .wrapper {
        position: relative;
    }

    ul,
    .custom-menu {
        width: 200px;
        max-height: 250px;
        overflow-x: hidden;
        overflow-y: auto;
    }

    li {
        position: static;
        padding: 1rem;
    }

    li .wrapper {
        position: absolute;
        z-index: 10;
        display: none;
        cursor: auto;
    }

    li:hover>.wrapper {
        display: none;
    }

    li>ul {
        margin: 0;
    }

    li .wrapper li {
        padding: 0.5rem;
    }
    </style>
</head>

<body>

    <div class="col-sm-10 text-center">


        <div class="row my-4">
            <div class="col-sm-4">
                <div class="btn-group dropright">
                    <button class="btn btn-secondary dropdown-toggle selected-country" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select Country
                    </button>
                    <div class="dropdown-menu custom-menu menu-countries" aria-labelledby="dropdownMenuButton">
                        <?php
                        include "db_conn.php";
                        $sql = "SELECT id, name FROM countries ORDER BY name ASC";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($country = $result->fetch_assoc()) {
                                $country_id = $country['id'];
                        ?>
                        <option value="<?php echo $country_id ?>" class="dropdown-item country-item">
                            <?php echo $country['name'] ?>
                        </option>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="btn-group dropright">
                    <button class="btn btn-info dropdown-toggle selected-state disabled" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select State
                    </button>
                    <div class="dropdown-menu custom-menu menu-states" aria-labelledby="dropdownMenuButton">
                        <option class="dropdown-item state-tem"></option>

                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="btn-group">
                    <button class="btn btn-secondary dropdown-toggle selected-city disabled" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select City
                    </button>
                    <div class="dropdown-menu custom-menu menu-cities" aria-labelledby="dropdownMenuButton">
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-outline-success submit">Success</button>

    </div>




    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script> -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"
        integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
    </script>
    <script>
    // select an country on click
    $('option.country-item').click(function() {
        console.log('clicked country item');
        var $menuItem = $(this);

        // remove previous selected class
        $('option.country-item.active').removeClass('active');

        // remove all prevoius states,cities in dropdown
        $('.menu-states').empty();
        $('.menu-cities').empty();

        // disable state and cities menu
        $('.selected-state').addClass('disabled');
        $('.selected-city').addClass('disabled');

        // reset to default for selected city and state
        $('button.selected-state').text('Select State')
        $('button.selected-city').text('Select City')

        // make selected country active
        $menuItem.addClass('active');
        // set selected country as dropdown button text
        $('button.selected-country').text($menuItem.text())
        console.log('clicked ', $menuItem.text(), ' id= ', $menuItem.val())

        // make post request for country id
        $.ajax({
            url: "world.php",
            type: "POST",
            data: {
                table: 'state',
                id: $menuItem.val()
            },
            success: function(data) {
                // populate states in dropdown list
                // console.log(data)
                // remove disabled option from state dropdown
                $('.selected-state').removeClass('disabled');
                $(data).each(function(idx, state) {
                    var state_option = "<option value='" + state['id'] +
                        "'class='dropdown-item state-item'>" + state['name'] +
                        "</option>";
                    $('.menu-states').append(state_option)

                    // console.log(state_option)
                })

                // make states dropdown button enabled

            },
            error: function(xhr, status, error) {
                // there was an error
                const errors = xhr.responseJSON;
                console.log(errors)
            }
        });
    });
    // select an state on click
    $(document).on('click', 'option.state-item', function() {
        console.log('clicked state item');
        var $menuItemState = $(this);

        // remove previous selected class
        $('option.state-item.active').removeClass('active');

        // remove all prevoius cities in dropdown
        $('.menu-cities').empty();

        // reset to default for selected city 
        $('button.selected-city').text('Select City')

        // make selected country active
        $menuItemState.addClass('active');

        // disable state and cities menu
        $('.selected-city').addClass('disabled');

        // set selected country as dropdown button text
        $('button.selected-state').text($menuItemState.text())
        console.log('clicked ', $menuItemState.text(), ' id= ', $menuItemState.val())

        // make post request for country id
        $.ajax({
            url: "world.php",
            type: "POST",
            data: {
                table: 'city',
                id: $menuItemState.val()
            },
            success: function(data) {
                // populate cities in dropdown list
                // remove disabled option from city dropdown
                $('.selected-city').removeClass('disabled');
                $(data).each(function(idx, city) {
                    var city_option = "<option value='" + city['id'] +
                        "'class='dropdown-item city-item'>" + city['name'] +
                        "</option>";
                    $('.menu-cities').append(city_option)

                    // console.log(state_option)
                })

                // make states dropdown button enabled

            },
            error: function(xhr, status, error) {
                // there was an error
                const errors = xhr.responseJSON;
                console.log(errors)
            }
        });
    });
    // select an city on click
    $(document).on('click', 'option.city-item', function() {
        console.log('clicked city item');
        var $menuItemCity = $(this);

        // remove previous selected class
        $('option.city-item.active').removeClass('active');

        // make selected country active
        $menuItemCity.addClass('active');
        // set selected country as dropdown button text
        $('button.selected-city').text($menuItemCity.text())
        console.log('clicked ', $menuItemCity.text(), ' id= ', $menuItemCity.val())

    });

    // on click submit button log country state city
    $(document).on('click', 'button.submit', function() {
        // get option.country-item.active
        const selectedCountry = $('option.country-item.active')

        // get option.state-item.active
        const selectedState = $('option.state-item.active')

        // get option.city-item.active
        const selectedCity = $('option.city-item.active')

        console.log(selectedCountry.val())
        console.log(selectedState.val())
        console.log(selectedCity.val())
    });
    </script>
</body>

</html>