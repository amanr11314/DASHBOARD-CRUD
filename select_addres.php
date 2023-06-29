<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous"> -->

    <title>Hello, world!</title>
    <style>
    /* .dropdown-menu {
        max-height: 280px;
        overflow-y: auto;
    }

    .dropdown-submenu {
        position: relative;
    } */

    /* .dropdown-submenu>.dropdown-menu { */
    /* left: 100%; */
    /* -6px gives dropdown-menu's padding+border */
    /* top: -6px; */
    /* } */

    /* 
    .dropdown-submenu:hover>.dropdown-menu,
    .dropdown-submenu>a:focus+.dropdown-menu { */
    /* :focus support is incomplete - pressing Tab sets focus to submenu, but that immediately hides submenu */
    /* display: block; */
    /* } */
    .wrapper {
        position: relative;
    }

    ul {
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
        display: block;
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
    <div class="wrapper">
        <ul>

            <li class="parent">Mno >
                <div class="wrapper">
                    <ul>
                        <li>Abc</li>
                        <li>Def</li>
                        <li>Ghi</li>
                        <li>Jkl</li>
                        <li class="parent">Mno >
                            <div class="wrapper">
                                <ul>
                                    <li>Abc</li>
                                    <li>Def</li>
                                    <li>Ghi</li>
                                    <li>Jkl</li>
                                    <li>Mno</li>
                                    <li>Pqr</li>
                                    <li>Stu</li>
                                    <li>Vw</li>
                                    <li>Xyz</li>
                                </ul>
                            </div>
                        </li>
                        <li>Pqr</li>
                        <li>Stu</li>
                        <li>Vw</li>
                        <li>Xyz</li>
                    </ul>
                </div>
            </li>

        </ul>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script> -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"
        integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>

    <!-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
    </script> -->
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

</html>