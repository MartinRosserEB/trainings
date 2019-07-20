<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190210195413 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Initial schema definition';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, first_name VARCHAR(180) DEFAULT NULL, family_name VARCHAR(180) DEFAULT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6497BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_type_user (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, training_type_id INT DEFAULT NULL, role VARCHAR(20) NOT NULL, INDEX IDX_341070B5A76ED395 (user_id), INDEX IDX_341070B518721C9D (training_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, training_type_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, start DATETIME DEFAULT NULL, end DATETIME DEFAULT NULL, INDEX IDX_D5128A8F61220EA6 (creator_id), INDEX IDX_D5128A8F18721C9D (training_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attendance (id INT AUTO_INCREMENT NOT NULL, training_id INT DEFAULT NULL, confirmation_user_id INT DEFAULT NULL, enlisting_ip VARCHAR(180) NOT NULL, enlistingTimestamp DATETIME DEFAULT NULL, confirmationTimestamp DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6DE30D91E4B74158 (enlisting_ip), INDEX IDX_6DE30D91BEFD98D1 (training_id), INDEX IDX_6DE30D912162FEC6 (confirmation_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE training_type_user ADD CONSTRAINT FK_341070B5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE training_type_user ADD CONSTRAINT FK_341070B518721C9D FOREIGN KEY (training_type_id) REFERENCES training_type (id)');
        $this->addSql('ALTER TABLE training ADD CONSTRAINT FK_D5128A8F61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE training ADD CONSTRAINT FK_D5128A8F18721C9D FOREIGN KEY (training_type_id) REFERENCES training_type (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D91BEFD98D1 FOREIGN KEY (training_id) REFERENCES training (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D912162FEC6 FOREIGN KEY (confirmation_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE training_type_user DROP FOREIGN KEY FK_341070B5A76ED395');
        $this->addSql('ALTER TABLE training DROP FOREIGN KEY FK_D5128A8F61220EA6');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D912162FEC6');
        $this->addSql('ALTER TABLE training_type_user DROP FOREIGN KEY FK_341070B518721C9D');
        $this->addSql('ALTER TABLE training DROP FOREIGN KEY FK_D5128A8F18721C9D');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D91BEFD98D1');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE training_type');
        $this->addSql('DROP TABLE training_type_user');
        $this->addSql('DROP TABLE training');
        $this->addSql('DROP TABLE attendance');
    }
}
