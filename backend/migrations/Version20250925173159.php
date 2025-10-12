<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250925173159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE absence (id SERIAL NOT NULL, absence_type_id INT NOT NULL, employe_id INT NOT NULL, half_day_id INT NOT NULL, date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_765AE0C9CCAA91B ON absence (absence_type_id)');
        $this->addSql('CREATE INDEX IDX_765AE0C91B65292 ON absence (employe_id)');
        $this->addSql('CREATE INDEX IDX_765AE0C97EFB0B88 ON absence (half_day_id)');
        $this->addSql('CREATE TABLE absence_type (id SERIAL NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE half_day (id SERIAL NOT NULL, label VARCHAR(255) NOT NULL, half_day_start TIME(0) WITHOUT TIME ZONE NOT NULL, half_day_end TIME(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE presence (id SERIAL NOT NULL, half_day_id INT NOT NULL, employe_id INT NOT NULL, date DATE NOT NULL, arrival TIME(0) WITHOUT TIME ZONE NOT NULL, depature TIME(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6977C7A57EFB0B88 ON presence (half_day_id)');
        $this->addSql('CREATE INDEX IDX_6977C7A51B65292 ON presence (employe_id)');
        $this->addSql('CREATE TABLE role (id SERIAL NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, role_id INT NOT NULL, manager_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, contract_weekly_hours INT DEFAULT NULL, contrat_start DATE DEFAULT NULL, contract_end DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8D93D649D60322AC ON "user" (role_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649783E3463 ON "user" (manager_id)');
        $this->addSql('CREATE TABLE user_half_day (user_id INT NOT NULL, half_day_id INT NOT NULL, PRIMARY KEY(user_id, half_day_id))');
        $this->addSql('CREATE INDEX IDX_4BAB658CA76ED395 ON user_half_day (user_id)');
        $this->addSql('CREATE INDEX IDX_4BAB658C7EFB0B88 ON user_half_day (half_day_id)');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C9CCAA91B FOREIGN KEY (absence_type_id) REFERENCES absence_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C91B65292 FOREIGN KEY (employe_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C97EFB0B88 FOREIGN KEY (half_day_id) REFERENCES half_day (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT FK_6977C7A57EFB0B88 FOREIGN KEY (half_day_id) REFERENCES half_day (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT FK_6977C7A51B65292 FOREIGN KEY (employe_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649783E3463 FOREIGN KEY (manager_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_half_day ADD CONSTRAINT FK_4BAB658CA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_half_day ADD CONSTRAINT FK_4BAB658C7EFB0B88 FOREIGN KEY (half_day_id) REFERENCES half_day (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE absence DROP CONSTRAINT FK_765AE0C9CCAA91B');
        $this->addSql('ALTER TABLE absence DROP CONSTRAINT FK_765AE0C91B65292');
        $this->addSql('ALTER TABLE absence DROP CONSTRAINT FK_765AE0C97EFB0B88');
        $this->addSql('ALTER TABLE presence DROP CONSTRAINT FK_6977C7A57EFB0B88');
        $this->addSql('ALTER TABLE presence DROP CONSTRAINT FK_6977C7A51B65292');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649D60322AC');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649783E3463');
        $this->addSql('ALTER TABLE user_half_day DROP CONSTRAINT FK_4BAB658CA76ED395');
        $this->addSql('ALTER TABLE user_half_day DROP CONSTRAINT FK_4BAB658C7EFB0B88');
        $this->addSql('DROP TABLE absence');
        $this->addSql('DROP TABLE absence_type');
        $this->addSql('DROP TABLE half_day');
        $this->addSql('DROP TABLE presence');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_half_day');
    }
}
