<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20190322172056 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE assessor_request (
          id INT AUTO_INCREMENT NOT NULL, 
          last_name VARCHAR(50) NOT NULL, 
          first_names VARCHAR(100) NOT NULL, 
          birth_name VARCHAR(50) DEFAULT NULL, 
          birthdate DATE NOT NULL, 
          birth_city VARCHAR(15) NOT NULL, 
          address VARCHAR(150) NOT NULL, 
          postal_code VARCHAR(15) NOT NULL, 
          city_insee VARCHAR(15) NOT NULL, 
          vote_city VARCHAR(15) NOT NULL, 
          office_number VARCHAR(10) NOT NULL, 
          email_address VARCHAR(255) NOT NULL, 
          phone VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', 
          assessor_city VARCHAR(15) NOT NULL, 
          office VARCHAR(15) NOT NULL, 
          created_at DATETIME NOT NULL, 
          updated_at DATETIME NOT NULL, 
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE assessor_request');
    }
}
