<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180409104206 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE username_role (username_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_DE92F17DED766068 (username_id), INDEX IDX_DE92F17DD60322AC (role_id), PRIMARY KEY(username_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE username_role ADD CONSTRAINT FK_DE92F17DED766068 FOREIGN KEY (username_id) REFERENCES username (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE username_role ADD CONSTRAINT FK_DE92F17DD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE username ADD salt VARCHAR(255) NOT NULL');
        $this->addSql('INSERT INTO ROLE(label) VALUE ("ROLE_USER")');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE username_role DROP FOREIGN KEY FK_DE92F17DD60322AC');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE username_role');
        $this->addSql('ALTER TABLE username DROP salt');
    }
}
