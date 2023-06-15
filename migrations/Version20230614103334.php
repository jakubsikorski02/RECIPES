<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230614103334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review_user (review_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6F279B513E2E969B (review_id), INDEX IDX_6F279B51A76ED395 (user_id), PRIMARY KEY(review_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review_recipe (review_id INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_50FEF6F73E2E969B (review_id), INDEX IDX_50FEF6F759D8A214 (recipe_id), PRIMARY KEY(review_id, recipe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE review_user ADD CONSTRAINT FK_6F279B513E2E969B FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review_user ADD CONSTRAINT FK_6F279B51A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review_recipe ADD CONSTRAINT FK_50FEF6F73E2E969B FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review_recipe ADD CONSTRAINT FK_50FEF6F759D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review_user DROP FOREIGN KEY FK_6F279B513E2E969B');
        $this->addSql('ALTER TABLE review_user DROP FOREIGN KEY FK_6F279B51A76ED395');
        $this->addSql('ALTER TABLE review_recipe DROP FOREIGN KEY FK_50FEF6F73E2E969B');
        $this->addSql('ALTER TABLE review_recipe DROP FOREIGN KEY FK_50FEF6F759D8A214');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE review_user');
        $this->addSql('DROP TABLE review_recipe');
    }
}
