<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221141930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id SERIAL NOT NULL, nom VARCHAR(100) NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, quantite_stock INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE client (id SERIAL NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, telephone VARCHAR(9) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE commande (id SERIAL NOT NULL, client_id INT DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6EEAA67D19EB6921 ON commande (client_id)');
        $this->addSql('CREATE TABLE detail_commande (id SERIAL NOT NULL, article_id INT DEFAULT NULL, commande_id INT DEFAULT NULL, quantite INT NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_98344FA67294869C ON detail_commande (article_id)');
        $this->addSql('CREATE INDEX IDX_98344FA682EA2E54 ON detail_commande (commande_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE detail_commande ADD CONSTRAINT FK_98344FA67294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE detail_commande ADD CONSTRAINT FK_98344FA682EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67D19EB6921');
        $this->addSql('ALTER TABLE detail_commande DROP CONSTRAINT FK_98344FA67294869C');
        $this->addSql('ALTER TABLE detail_commande DROP CONSTRAINT FK_98344FA682EA2E54');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE detail_commande');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
