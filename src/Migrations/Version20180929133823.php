<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180929133823 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `column` CHANGE board_id board_id INT NOT NULL');
        $this->addSql('ALTER TABLE `column` ADD CONSTRAINT FK_7D53877EE7EC5785 FOREIGN KEY (board_id) REFERENCES board (id)');
        $this->addSql('CREATE INDEX IDX_7D53877EE7EC5785 ON `column` (board_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `column` DROP FOREIGN KEY FK_7D53877EE7EC5785');
        $this->addSql('DROP INDEX IDX_7D53877EE7EC5785 ON `column`');
        $this->addSql('ALTER TABLE `column` CHANGE board_id board_id VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
