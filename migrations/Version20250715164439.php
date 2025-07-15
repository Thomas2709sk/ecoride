<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715164439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carpools (id INT AUTO_INCREMENT NOT NULL, driver_id INT NOT NULL, car_id INT NOT NULL, day DATE NOT NULL, begin TIME NOT NULL, end TIME NOT NULL, address_start VARCHAR(255) NOT NULL, address_end VARCHAR(255) NOT NULL, places_available INT NOT NULL, price INT NOT NULL, status ENUM(\'A venir\', \'En cours\', \'Terminé\', \'Confirmé\', \'Vérification par la plateforme\') NOT NULL DEFAULT \'A venir\', carpool_number VARCHAR(255) NOT NULL, is_ecological TINYINT(1) NOT NULL, duration INT NOT NULL, startLat DOUBLE PRECISION DEFAULT NULL, startLon DOUBLE PRECISION DEFAULT NULL, endLat DOUBLE PRECISION DEFAULT NULL, endLon DOUBLE PRECISION DEFAULT NULL, INDEX IDX_89351C00C3423909 (driver_id), INDEX IDX_89351C00C3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carpools_users (users_id INT NOT NULL, carpools_id INT NOT NULL, is_confirmed TINYINT(1) DEFAULT 0 NOT NULL, is_ended TINYINT(1) NOT NULL, is_answered TINYINT(1) NOT NULL, INDEX IDX_12BA1B6A67B3B43D (users_id), INDEX IDX_12BA1B6AD6855CC7 (carpools_id), PRIMARY KEY(users_id, carpools_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cars (id INT AUTO_INCREMENT NOT NULL, driver_id INT NOT NULL, brand VARCHAR(30) NOT NULL, model VARCHAR(30) NOT NULL, color VARCHAR(30) NOT NULL, seats INT NOT NULL, plate_number VARCHAR(15) NOT NULL, first_registration DATE NOT NULL, energy VARCHAR(15) NOT NULL, INDEX IDX_95C71D14C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE drivers (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, animals TINYINT(1) NOT NULL, smoking TINYINT(1) NOT NULL, preferences VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E410C307A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reviews (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, driver_id INT NOT NULL, carpool_id INT NOT NULL, rate INT NOT NULL, commentary LONGTEXT NOT NULL, validate TINYINT(1) NOT NULL, INDEX IDX_6970EB0FA76ED395 (user_id), INDEX IDX_6970EB0FC3423909 (driver_id), INDEX IDX_6970EB0F9A6F0DAE (carpool_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, pseudo VARCHAR(30) NOT NULL, photo VARCHAR(255) DEFAULT NULL, credits INT NOT NULL, is_verified TINYINT(1) NOT NULL, is_passenger TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carpools ADD CONSTRAINT FK_89351C00C3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id)');
        $this->addSql('ALTER TABLE carpools ADD CONSTRAINT FK_89351C00C3C6F69F FOREIGN KEY (car_id) REFERENCES cars (id)');
        $this->addSql('ALTER TABLE carpools_users ADD CONSTRAINT FK_12BA1B6A67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE carpools_users ADD CONSTRAINT FK_12BA1B6AD6855CC7 FOREIGN KEY (carpools_id) REFERENCES carpools (id)');
        $this->addSql('ALTER TABLE cars ADD CONSTRAINT FK_95C71D14C3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id)');
        $this->addSql('ALTER TABLE drivers ADD CONSTRAINT FK_E410C307A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FC3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F9A6F0DAE FOREIGN KEY (carpool_id) REFERENCES carpools (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carpools DROP FOREIGN KEY FK_89351C00C3423909');
        $this->addSql('ALTER TABLE carpools DROP FOREIGN KEY FK_89351C00C3C6F69F');
        $this->addSql('ALTER TABLE carpools_users DROP FOREIGN KEY FK_12BA1B6A67B3B43D');
        $this->addSql('ALTER TABLE carpools_users DROP FOREIGN KEY FK_12BA1B6AD6855CC7');
        $this->addSql('ALTER TABLE cars DROP FOREIGN KEY FK_95C71D14C3423909');
        $this->addSql('ALTER TABLE drivers DROP FOREIGN KEY FK_E410C307A76ED395');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FA76ED395');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FC3423909');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F9A6F0DAE');
        $this->addSql('DROP TABLE carpools');
        $this->addSql('DROP TABLE carpools_users');
        $this->addSql('DROP TABLE cars');
        $this->addSql('DROP TABLE drivers');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
