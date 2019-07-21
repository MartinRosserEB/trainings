<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190721200858 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE training (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, training_type_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, place VARCHAR(255) DEFAULT NULL, public VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, start DATETIME DEFAULT NULL, end DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_D5128A8F3BB42E1D (public), INDEX IDX_D5128A8F61220EA6 (creator_id), INDEX IDX_D5128A8F18721C9D (training_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attendance (id INT AUTO_INCREMENT NOT NULL, training_id INT DEFAULT NULL, person_id INT DEFAULT NULL, confirmation_user_id INT DEFAULT NULL, enlisting_ip VARCHAR(180) DEFAULT NULL, enlistingTimestamp DATETIME DEFAULT NULL, confirmationTimestamp DATETIME DEFAULT NULL, INDEX IDX_6DE30D91BEFD98D1 (training_id), INDEX IDX_6DE30D91217BBB47 (person_id), INDEX IDX_6DE30D912162FEC6 (confirmation_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_type_person (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, training_type_id INT DEFAULT NULL, role VARCHAR(20) NOT NULL, active_since DATETIME NOT NULL, active_until DATETIME DEFAULT NULL, INDEX IDX_B360C099217BBB47 (person_id), INDEX IDX_B360C09918721C9D (training_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) DEFAULT NULL, api_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, first_name VARCHAR(180) DEFAULT NULL, family_name VARCHAR(180) DEFAULT NULL, birthdate DATETIME DEFAULT NULL, street VARCHAR(180) DEFAULT NULL, street_no INT DEFAULT NULL, city VARCHAR(180) DEFAULT NULL, zip_code VARCHAR(10) DEFAULT NULL, INDEX IDX_34DCD176A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE training ADD CONSTRAINT FK_D5128A8F61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE training ADD CONSTRAINT FK_D5128A8F18721C9D FOREIGN KEY (training_type_id) REFERENCES training_type (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D91BEFD98D1 FOREIGN KEY (training_id) REFERENCES training (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D91217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D912162FEC6 FOREIGN KEY (confirmation_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE training_type_person ADD CONSTRAINT FK_B360C099217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE training_type_person ADD CONSTRAINT FK_B360C09918721C9D FOREIGN KEY (training_type_id) REFERENCES training_type (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D91BEFD98D1');
        $this->addSql('ALTER TABLE training DROP FOREIGN KEY FK_D5128A8F18721C9D');
        $this->addSql('ALTER TABLE training_type_person DROP FOREIGN KEY FK_B360C09918721C9D');
        $this->addSql('ALTER TABLE training DROP FOREIGN KEY FK_D5128A8F61220EA6');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D912162FEC6');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176A76ED395');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D91217BBB47');
        $this->addSql('ALTER TABLE training_type_person DROP FOREIGN KEY FK_B360C099217BBB47');
        $this->addSql('DROP TABLE training');
        $this->addSql('DROP TABLE attendance');
        $this->addSql('DROP TABLE training_type');
        $this->addSql('DROP TABLE training_type_person');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE person');
    }
}
