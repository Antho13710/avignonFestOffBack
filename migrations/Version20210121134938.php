<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210121134938 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE date ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE date ADD CONSTRAINT FK_AA9E377AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AA9E377AA76ED395 ON date (user_id)');
        $this->addSql('ALTER TABLE day_off ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE day_off ADD CONSTRAINT FK_926C726CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_926C726CA76ED395 ON day_off (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE date DROP FOREIGN KEY FK_AA9E377AA76ED395');
        $this->addSql('DROP INDEX IDX_AA9E377AA76ED395 ON date');
        $this->addSql('ALTER TABLE date DROP user_id');
        $this->addSql('ALTER TABLE day_off DROP FOREIGN KEY FK_926C726CA76ED395');
        $this->addSql('DROP INDEX IDX_926C726CA76ED395 ON day_off');
        $this->addSql('ALTER TABLE day_off DROP user_id');
    }
}
