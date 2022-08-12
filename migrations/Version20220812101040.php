<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220812101040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD category_id INT NOT NULL, ADD abstract LONGTEXT NOT NULL, ADD quantity INT NOT NULL, ADD price DOUBLE PRECISION NOT NULL, ADD min_players INT NOT NULL, ADD max_players INT DEFAULT NULL, ADD minimum_age INT DEFAULT NULL, ADD duration TIME DEFAULT NULL, ADD theme VARCHAR(255) DEFAULT NULL, ADD mecanism VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD img1 VARCHAR(45) NOT NULL, ADD img2 VARCHAR(45) DEFAULT NULL, ADD img3 VARCHAR(45) DEFAULT NULL, CHANGE name name VARCHAR(100) NOT NULL, CHANGE slug slug VARCHAR(100) NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE img editor VARCHAR(45) DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C15E237E06 ON category (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1989D9B62 ON category (slug)');
        $this->addSql('CREATE INDEX IDX_64C19C112469DE2 ON category (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C112469DE2');
        $this->addSql('DROP INDEX UNIQ_64C19C15E237E06 ON category');
        $this->addSql('DROP INDEX UNIQ_64C19C1989D9B62 ON category');
        $this->addSql('DROP INDEX IDX_64C19C112469DE2 ON category');
        $this->addSql('ALTER TABLE category ADD img VARCHAR(45) DEFAULT NULL, DROP category_id, DROP abstract, DROP quantity, DROP price, DROP min_players, DROP max_players, DROP minimum_age, DROP duration, DROP editor, DROP theme, DROP mecanism, DROP created_at, DROP img1, DROP img2, DROP img3, CHANGE name name VARCHAR(45) NOT NULL, CHANGE slug slug VARCHAR(45) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL');
    }
}
