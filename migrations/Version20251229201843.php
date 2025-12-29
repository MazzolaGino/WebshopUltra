<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251229201843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(191) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE coupons CHANGE type type ENUM(\'percent\', \'amount\')');
        $this->addSql('ALTER TABLE orders CHANGE status status ENUM(\'pending\',\'paid\',\'processing\',\'shipped\',\'delivered\',\'cancelled\')');
        $this->addSql('ALTER TABLE products CHANGE type type ENUM(\'standard\',\'external\')');
        $this->addSql('ALTER TABLE user_addresses CHANGE type type ENUM(\'shipping\', \'billing\', \'both\')');
        $this->addSql('ALTER TABLE users CHANGE role role ENUM(\'customer\',\'admin\',\'editor\',\'logistics\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('ALTER TABLE coupons CHANGE type type ENUM(\'percent\', \'amount\') DEFAULT NULL');
        $this->addSql('ALTER TABLE orders CHANGE status status ENUM(\'pending\', \'paid\', \'processing\', \'shipped\', \'delivered\', \'cancelled\') DEFAULT NULL');
        $this->addSql('ALTER TABLE products CHANGE type type ENUM(\'standard\', \'external\') DEFAULT NULL');
        $this->addSql('ALTER TABLE user_addresses CHANGE type type ENUM(\'shipping\', \'billing\', \'both\') DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE role role ENUM(\'customer\', \'admin\', \'editor\', \'logistics\') DEFAULT NULL');
    }
}
