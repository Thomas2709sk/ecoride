<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613132743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE carpools (id INT AUTO_INCREMENT NOT NULL, driver_id INT NOT NULL, car_id INT NOT NULL, day DATE NOT NULL, begin TIME NOT NULL, end TIME NOT NULL, address_start VARCHAR(255) NOT NULL, address_end VARCHAR(255) NOT NULL, day_end DATE NOT NULL, places_available INT NOT NULL, price INT NOT NULL, status VARCHAR(255) NOT NULL, carpool_number VARCHAR(255) NOT NULL, INDEX IDX_89351C00C3423909 (driver_id), INDEX IDX_89351C00C3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE carpools_users (carpools_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_12BA1B6AD6855CC7 (carpools_id), INDEX IDX_12BA1B6A67B3B43D (users_id), PRIMARY KEY(carpools_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cars (id INT AUTO_INCREMENT NOT NULL, driver_id INT NOT NULL, brand VARCHAR(30) NOT NULL, model VARCHAR(30) NOT NULL, color VARCHAR(30) NOT NULL, seats INT NOT NULL, plate_number VARCHAR(15) NOT NULL, first_registration DATE NOT NULL, INDEX IDX_95C71D14C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE drivers (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, animals TINYINT(1) NOT NULL, smoking TINYINT(1) NOT NULL, preferences VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E410C307A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reviews (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, driver_id INT NOT NULL, carpool_id INT NOT NULL, rate INT NOT NULL, commentary LONGTEXT NOT NULL, validate TINYINT(1) NOT NULL, INDEX IDX_6970EB0FA76ED395 (user_id), INDEX IDX_6970EB0FC3423909 (driver_id), INDEX IDX_6970EB0F9A6F0DAE (carpool_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) NOT NULL, pseudo VARCHAR(30) NOT NULL, photo VARCHAR(255) DEFAULT NULL, credits INT NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpools ADD CONSTRAINT FK_89351C00C3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpools ADD CONSTRAINT FK_89351C00C3C6F69F FOREIGN KEY (car_id) REFERENCES cars (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpools_users ADD CONSTRAINT FK_12BA1B6AD6855CC7 FOREIGN KEY (carpools_id) REFERENCES carpools (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpools_users ADD CONSTRAINT FK_12BA1B6A67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cars ADD CONSTRAINT FK_95C71D14C3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE drivers ADD CONSTRAINT FK_E410C307A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FC3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F9A6F0DAE FOREIGN KEY (carpool_id) REFERENCES carpools (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE carpools DROP FOREIGN KEY FK_89351C00C3423909
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpools DROP FOREIGN KEY FK_89351C00C3C6F69F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpools_users DROP FOREIGN KEY FK_12BA1B6AD6855CC7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpools_users DROP FOREIGN KEY FK_12BA1B6A67B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cars DROP FOREIGN KEY FK_95C71D14C3423909
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE drivers DROP FOREIGN KEY FK_E410C307A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FC3423909
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F9A6F0DAE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE carpools
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE carpools_users
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cars
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE drivers
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reviews
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE users
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
