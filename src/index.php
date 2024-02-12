<?php

$dsn = 'mysql:host=mysql;dbname=petsdb';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $tables = [
        'pets' => "CREATE TABLE `pets` (
        `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
        `name` VARCHAR(100) NOT NULL,
        `breed` VARCHAR(100) NOT NULL,
        `age` INT NOT NULL,
        `type` VARCHAR(100) NOT NULL,
        `microchip` CHAR(10) UNIQUE NULL CHECK (LENGTH(`microchip`) = 10),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL
    )",
        'owners' => "CREATE TABLE `owners` (
        `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) UNIQUE NOT NULL,
        `phone` CHAR(12) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL
    )",
        'vaccinations' => "CREATE TABLE `vaccinations` (
        `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
        `name` VARCHAR(100) NOT NULL,
        `date` DATE NOT NULL,
        `pet_id` INT UNSIGNED NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL,
        FOREIGN KEY (`pet_id`) REFERENCES `pets`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
    )",
        'pets_owners' => "CREATE TABLE `pets_owners` (
        `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
        `pet_id` INT UNSIGNED NOT NULL,
        `owner_id` INT UNSIGNED NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL,
        FOREIGN KEY (`pet_id`) REFERENCES `pets`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (`owner_id`) REFERENCES `owners`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
    )"
    ];

    foreach ($tables as $table => $sql) {
        $pdo->exec($sql);
    }

    $insertPetsStmt = $pdo->prepare(
        "INSERT INTO `pets` (`name`, `breed`, `age`, `type`, `microchip`) 
                        VALUES (:name, :breed, :age, :type, :microchip)"
    );
    $pets = [
        ['name' => 'Charlie', 'breed' => 'Labrador', 'age' => 3, 'type' => 'Dog', 'microchip' => '1234567890'],
        ['name' => 'Bella', 'breed' => 'Siamese', 'age' => 5, 'type' => 'Cat', 'microchip' => null],
        ['name' => 'Max', 'breed' => 'Beagle', 'age' => 4, 'type' => 'Dog', 'microchip' => '0987654321'],
        ['name' => 'Lucy', 'breed' => 'Persian', 'age' => 2, 'type' => 'Cat', 'microchip' => '1122334455'],
        ['name' => 'Buddy', 'breed' => 'Sphynx', 'age' => 1, 'type' => 'Cat', 'microchip' => null],
    ];
    foreach ($pets as $pet) {
        $insertPetsStmt->execute($pet);
    }

    $insertOwnersStmt = $pdo->prepare(
        "INSERT INTO `owners` (`name`, `email`, `phone`) 
                        VALUES (:name, :email, :phone)");
    $owners = [
        ['name' => 'Ivan Petrov', 'email' => 'ivan.petrov@example.com', 'phone' => '380501234567'],
        ['name' => 'Olena Koval', 'email' => 'olena.koval@example.com', 'phone' => '380671234567'],
        ['name' => 'Andriy Melnyk', 'email' => 'andriy.melnyk@example.com', 'phone' => '380931234567'],
    ];
    foreach ($owners as $owner) {
        $insertOwnersStmt->execute($owner);
    }

    $insertVaccinationsStmt = $pdo->prepare(
        "INSERT INTO `vaccinations` (`name`, `date`, `pet_id`) 
                        VALUES (:name, :date, :pet_id)");
    $vaccinations = [
        ['name' => 'Rabies', 'date' => '2023-01-10', 'pet_id' => 1],
        ['name' => 'Distemper', 'date' => '2023-02-15', 'pet_id' => 1],
        ['name' => 'Rabies', 'date' => '2023-03-05', 'pet_id' => 2],
        ['name' => 'Leukemia', 'date' => '2023-04-20', 'pet_id' => 2],
        ['name' => 'Rabies', 'date' => '2023-05-25', 'pet_id' => 3],
        ['name' => 'Distemper', 'date' => '2023-06-30', 'pet_id' => 3],
        ['name' => 'Leukemia', 'date' => '2023-07-15', 'pet_id' => 4],
        ['name' => 'Rabies', 'date' => '2023-08-05', 'pet_id' => 4],
        ['name' => 'Distemper', 'date' => '2023-09-10', 'pet_id' => 5],
        ['name' => 'Leukemia', 'date' => '2023-10-15', 'pet_id' => 5],
    ];
    foreach ($vaccinations as $vaccination) {
        $insertVaccinationsStmt->execute($vaccination);
    }

    $insertPetsOwnersStmt = $pdo->prepare(
        "INSERT INTO `pets_owners` (`pet_id`, `owner_id`) 
                        VALUES (:pet_id, :owner_id)");
    $petsOwners = [
        ['pet_id' => 1, 'owner_id' => 1],
        ['pet_id' => 2, 'owner_id' => 1],
        ['pet_id' => 3, 'owner_id' => 2],
        ['pet_id' => 4, 'owner_id' => 2],
        ['pet_id' => 5, 'owner_id' => 3],
    ];
    foreach ($petsOwners as $petOwner) {
        $insertPetsOwnersStmt->execute($petOwner);
    }

    $updatePetsStmt = $pdo->prepare("UPDATE `pets` SET `age` = :age WHERE `name` = :name");
    $updatePetsStmt->execute(['age' => 4, 'name' => 'Buddy']);

    $updateOwnersStmt = $pdo->prepare("UPDATE `owners` SET `phone` = :phone WHERE `name` = :name");
    $updateOwnersStmt->execute(['phone' => '380951234567', 'name' => 'Ivan Petrov']);

    $deletePetStmt = $pdo->prepare("DELETE FROM `pets` WHERE `name` = :name");
    $deletePetStmt->execute(['name' => 'Max']);

    $selectPetsQuery = "SELECT `name`, `type` FROM `pets`";
    $stmt = $pdo->query($selectPetsQuery);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Pets and their types:\n";
    foreach ($pets as $pet) {
        echo "{$pet['name']} is a {$pet['type']}\n";
    }

    $selectVaccinationsQuery = "
        SELECT p.`name` AS pet_name, v.`name` AS vaccination, v.`date`
            FROM `pets` p
            JOIN `vaccinations` v ON p.`id` = v.`pet_id`
        ORDER BY p.`name`, v.`date`";
    $stmt = $pdo->query($selectVaccinationsQuery);
    $vaccinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Vaccination records:\n";
    foreach ($vaccinations as $record) {
        echo "{$record['pet_name']} was vaccinated against {$record['vaccination']} on {$record['date']}\n";
    }

    $selectVaccinationCountQuery = "
        SELECT p.`name`, COUNT(v.`id`) AS vaccination_count
            FROM `pets` p
            JOIN `vaccinations` v ON p.`id` = v.`pet_id`
        GROUP BY p.`name`
        ORDER BY vaccination_count DESC";
    $stmt = $pdo->query($selectVaccinationCountQuery);
    $vaccinationCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Vaccination counts for each pet:\n";
    foreach ($vaccinationCounts as $count) {
        echo "{$count['name']} has {$count['vaccination_count']} vaccinations\n";
    }

    $selectOwnersWithMultiplePetsQuery = "
        SELECT o.`name`, COUNT(po.`pet_id`) AS pets_owned
            FROM `owners` o
            JOIN `pets_owners` po ON o.`id` = po.`owner_id`
        GROUP BY o.`name`
        HAVING COUNT(po.`pet_id`) > 1";
    $stmt = $pdo->query($selectOwnersWithMultiplePetsQuery);
    $ownersWithMultiplePets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Owners with more than one pet:\n";
    foreach ($ownersWithMultiplePets as $owner) {
        echo "{$owner['name']} owns {$owner['pets_owned']} pets\n";
    }

    foreach (array_reverse(array_keys($tables)) as $table) {
        $pdo->exec("DROP TABLE `$table`");
    }

    echo "All database operations completed successfully.\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    die();
}
