<html>
    <?php include('navigation.php') ?>
    <body onload="loadDoc()">
        <h1 class="text-center">CD Collection Filters</h1>
        <div class="row m-5">
            <div class="col">
                <label class="mr-3">Country</label>
                <input
                    onkeyup="showResultByCountry(value); country=value;"
                    onmouseout="showResultByCountry(value); country=value;">
                <button
                    class="btn btn-info ml-3"
                    onclick="console.log(country);showResultByCountry(country)">Filter</button>
            </div>
            <div class="col">
                <label class="mr-3">Price</label>
                <input onkeyup="price = value;">
                <button
                    class="btn btn-info ml-3"
                    onclick="console.log(price);showResultbyPrice(price,'<');"><</button>
                    <button class="btn btn-info ml-3"
                    onclick="console.log(price);showResultbyPrice(price,'>');">></button>
            </div>
        </div>
        <div class="row d-flex justify-content-around">
            <div id="titleList"></div>
            <div id="countryList"></div>
        </div>
        <script src="scripts.js"></script>
    </body>
</html>