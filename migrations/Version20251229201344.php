<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251229201344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coupons CHANGE type type ENUM(\'percent\', \'amount\')');
        $this->addSql('ALTER TABLE orders CHANGE status status ENUM(\'pending\',\'paid\',\'processing\',\'shipped\',\'delivered\',\'cancelled\')');
        $this->addSql('ALTER TABLE products CHANGE type type ENUM(\'standard\',\'external\')');
        $this->addSql('ALTER TABLE user_addresses CHANGE type type ENUM(\'shipping\', \'billing\', \'both\')');
        $this->addSql('ALTER TABLE users CHANGE role role ENUM(\'customer\',\'admin\',\'editor\',\'logistics\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coupons CHANGE type type ENUM(\'percent\', \'amount\') DEFAULT NULL');
        $this->addSql('ALTER TABLE orders CHANGE status status ENUM(\'pending\', \'paid\', \'processing\', \'shipped\', \'delivered\', \'cancelled\') DEFAULT NULL');
        $this->addSql('ALTER TABLE products CHANGE type type ENUM(\'standard\', \'external\') DEFAULT NULL');
        $this->addSql('ALTER TABLE user_addresses CHANGE type type ENUM(\'shipping\', \'billing\', \'both\') DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE role role ENUM(\'customer\', \'admin\', \'editor\', \'logistics\') DEFAULT NULL');
    }
}
