<body>
    <div class="dropdown open py-4 pl-4 wrapper">
        <button class="btn btn-info dropdown-toggle" id="dLabel" type="button" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            Select Country
            <span class="caret"></span>
        </button>
        <ul id="countriesList" class="dropdown-menu multi-level dropdown-countries my-4" aria-labelledby="dLabel">
            <?php
            include "db_conn.php";
            // Display employees in table
            $sql = "SELECT id,name from countries ORDER BY name ASC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($data = $result->fetch_assoc()) { ?>
            <li class="dropdown-submenu parent">
                <a role="button" class="px-4" href="#"><?php echo $data['name']; ?> </a>
                <div class="wrapper">
                    <ul class="dropdown-menu states-submenu">
                        <li><a href="#">3rd level</a></li>
                        <li><a href="#">3rd level</a></li>
                    </ul>
                </div>
            </li>
            <?php }
            }
            ?>
            <!-- <li class="dropdown-submenu">
                <a href="#">Action 1</a>
                <ul class="dropdown-menu">
                    <li><a href="#">3rd level</a></li>
                    <li><a href="#">3rd level</a></li>
                </ul>
            </li>
            <li class="dropdown-submenu">
                <a href="#">Action 2</a>
                <ul class="dropdown-menu">
                    <li><a href="#">3rd level B</a></li>
                    <li><a href="#">3rd level B</a></li>
                </ul>
            </li> -->
        </ul>
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
    // whenever we hover over a menu item that has a submenu
    $('li.parent').on('mouseover', function() {
        var $menuItem = $(this),
            $submenuWrapper = $('> .wrapper', $menuItem);

        // grab the menu item's position relative to its positioned parent
        var menuItemPos = $menuItem.position();

        // place the submenu in the correct position relevant to the menu item
        $submenuWrapper.css({
            top: menuItemPos.top,
            left: menuItemPos.left + Math.round($menuItem.outerWidth() * 0.75)
        });
    });
    </script>
</body>