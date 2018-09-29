<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180929135238 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE card DROP id_column, CHANGE board_column_id board_column_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3CA372FE FOREIGN KEY (board_column_id) REFERENCES `column` (id)');
        $this->addSql('CREATE INDEX IDX_161498D3CA372FE ON card (board_column_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3CA372FE');
        $this->addSql('DROP INDEX IDX_161498D3CA372FE ON card');
        $this->addSql('ALTER TABLE card ADD id_column VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE board_column_id board_column_id INT NOT NULL');
    }
}
