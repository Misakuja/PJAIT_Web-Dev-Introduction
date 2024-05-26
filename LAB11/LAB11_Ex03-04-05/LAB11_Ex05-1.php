<?php
session_start();

require_once "LAB11_Ex03-Car.php";
require_once "LAB11_Ex03-NewCar.php";
require_once "LAB11-Ex04-InsuranceCar.php";

$carChoice = null;
$firstFormSubmitted = false;

if (!isset($_SESSION['cars'])) {
    $_SESSION['cars'] = [];
    $_SESSION['car_count'] = 0;
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['carChoice'])) {
    $carChoice = $_POST['carChoice'];
    $firstFormSubmitted = true;
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['model'])) {
    $model = $_POST['model'];
    $price = $_POST['price'];
    $exchangeRate = $_POST['exchangeRate'];
    $alarm = isset($_POST['alarm']);
    $radio = isset($_POST['radio']);
    $climatronic = isset($_POST['climatronic']);
    $firstOwner = isset($_POST['firstOwner']);
    $years = $_POST['years'] ?? null;

    $car = createObject($carChoice, $model, $price, $exchangeRate, $alarm, $radio, $climatronic, $firstOwner, $years);

    $_SESSION['cars'][] = [
        'model' => $model,
        'price' => $price,
        'exchangeRate' => $exchangeRate,
        'alarm' => $alarm,
        'radio' => $radio,
        'climatronic' => $climatronic,
        'firstOwner' => $firstOwner,
        'years' => $years
    ];

    $_SESSION['car_count']++;
}

function createObject($carChoice, $model, $price, $exchangeRate, $alarm, $radio, $climatronic, $firstOwner, $years) {
    return match ($carChoice) {
        "car" => new Car($model, $price, $exchangeRate),
        "newCar" => new NewCar($model, $price, $exchangeRate, $alarm, $radio, $climatronic),
        "insuranceCar" => new InsuranceCar($model, $price, $exchangeRate, $alarm, $radio, $climatronic, $firstOwner, $years),
        default => null,
    };
}

// delete / edit / check / calc price | logic below
function deleteCar($index) : void {
    unset ($_SESSION['cars'][$index]);
    $_SESSION['car_count']--;
    $_SESSION['cars'] = array_values($_SESSION['cars']);
}
if(isset($_POST["deleteCar"]) && isset($_POST["index"])) {
    $index = $_POST["index"];
    deleteCar($index);
}
if(isset($_POST["calculatePrice"]) && isset($_POST["index"])) {
    $index = $_POST["index"];
    $car = $_SESSION['cars'][$index];
    $carValue = $car->value();
    echo $carValue;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Site</title>
    <link href="-" rel="stylesheet" type="text/css">
</head>
<body>
<div class="carCounter">
    <?php
    echo "Car Counter: " . $_SESSION['car_count'];
    ?>
</div>
<div class="formChoice">
    <form method='post' action="">
        <fieldset>
            <label>
                <select name="carChoice">
                    <option value="car">Car</option>
                    <option value="newCar">NewCar</option>
                    <option value="insuranceCar">InsuranceCar</option>
                </select>
            </label>
            <button type='submit'>Send</button>
        </fieldset>
    </form>
</div>
<?php if ($firstFormSubmitted) : ?>
<div class="formInput">
    <form method='post' action="">
        <fieldset>
            <label for="model">Model:</label>
            <input type='text' id='model' name='model' required>

            <label for="price">Price:</label>
            <input type='number' id='price' name='price' required>

            <label for="exchangeRate">Exchange Rate:</label>
            <input type='number' id='exchangeRate' name='exchangeRate' required>


            <?php if ($carChoice === 'newCar' || $carChoice === "insuranceCar"): ?>
                <label for="alarm">Alarm:</label>
                <input type='checkbox' id='alarm' name='alarm'>

                <label for="radio">Radio:</label>
                <input type='checkbox' id='radio' name='radio'>

                <label for="climatronic">Climatronic:</label>
                <input type='checkbox' id='climatronic' name='climatronic'>
            <?php endif ?>

            <?php if ($carChoice === 'insuranceCar'): ?>
                <label for="firstOwner">First Owner:</label>
                <input type='checkbox' id='firstOwner' name='firstOwner'>

                <label for="years">Years:</label>
                <input type='number' id='years' name='years' required>
            <?php endif ?>

            <button type='submit'>Submit</button>
        </fieldset>
    </form>
    <?php endif ?>
</div>
<div class="carList">
    <ul>
        <?php foreach ($_SESSION['cars'] as $index => $carData) : ?>
            <?php echo "<li>" . $carData['model'] . " | " . $carData['price'] . " | " . $carData['exchangeRate'] . "</li>"; ?>
            <form action="" method="post">
                <button type="submit" name="calculatePrice">Calculate Price</button>
            </form>

            <form action="" method="post">
                <button type="submit" name="detailsCar">Check or edit Car Details</button>
            </form>
            <form action="" method="post">
                <button type="submit" name="deleteCar">Delete Car</button>
                <input type="hidden" name="index" value="<?php echo $index ?>">
            </form>
        <?php endforeach ?>
    </ul>
</div>

</body>
</html>
