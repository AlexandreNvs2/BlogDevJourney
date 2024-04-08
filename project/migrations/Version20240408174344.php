<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240408174344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_389B7835E237E06 (name), UNIQUE INDEX UNIQ_389B783989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_post (tag_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_B485D33BBAD26311 (tag_id), INDEX IDX_B485D33B4B89032C (post_id), PRIMARY KEY(tag_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_post ADD CONSTRAINT FK_B485D33BBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_post ADD CONSTRAINT FK_B485D33B4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1989D9B62 ON category (slug)');
        $this->addSql('ALTER TABLE category_post DROP FOREIGN KEY FK_D11116CA8C5EAFB7');
        $this->addSql('DROP INDEX IDX_D11116CA8C5EAFB7 ON category_post');
        $this->addSql('DROP INDEX `primary` ON category_post');
        $this->addSql('ALTER TABLE category_post CHANGE categories_posts category_id INT NOT NULL');
        $this->addSql('ALTER TABLE category_post ADD CONSTRAINT FK_D11116CA12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_D11116CA12469DE2 ON category_post (category_id)');
        $this->addSql('ALTER TABLE category_post ADD PRIMARY KEY (category_id, post_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_post DROP FOREIGN KEY FK_B485D33BBAD26311');
        $this->addSql('ALTER TABLE tag_post DROP FOREIGN KEY FK_B485D33B4B89032C');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_post');
        $this->addSql('DROP INDEX UNIQ_64C19C1989D9B62 ON category');
        $this->addSql('ALTER TABLE category CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE category_post DROP FOREIGN KEY FK_D11116CA12469DE2');
        $this->addSql('DROP INDEX IDX_D11116CA12469DE2 ON category_post');
        $this->addSql('DROP INDEX `PRIMARY` ON category_post');
        $this->addSql('ALTER TABLE category_post CHANGE category_id categories_posts INT NOT NULL');
        $this->addSql('ALTER TABLE category_post ADD CONSTRAINT FK_D11116CA8C5EAFB7 FOREIGN KEY (categories_posts) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D11116CA8C5EAFB7 ON category_post (categories_posts)');
        $this->addSql('ALTER TABLE category_post ADD PRIMARY KEY (categories_posts, post_id)');
    }
}
