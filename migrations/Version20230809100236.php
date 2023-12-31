<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230809100236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD id_user_id INT NOT NULL, ADD id_car_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939879F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398E5F14372 FOREIGN KEY (id_car_id) REFERENCES car (id)');
        $this->addSql('CREATE INDEX IDX_F529939879F37AE5 ON `order` (id_user_id)');
        $this->addSql('CREATE INDEX IDX_F5299398E5F14372 ON `order` (id_car_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939879F37AE5');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398E5F14372');
        $this->addSql('DROP INDEX IDX_F529939879F37AE5 ON `order`');
        $this->addSql('DROP INDEX IDX_F5299398E5F14372 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP id_user_id, DROP id_car_id');
    }
}
