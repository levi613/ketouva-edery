<?php

namespace App\Command;

use App\Entity\Annee;
use App\Entity\JourMois;
use App\Entity\JourSemaine;
use App\Entity\Mois;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class InsereDatesHebreu extends Command
{
    private $em;

    protected static $defaultName = 'app:insere-dates-hebreu';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName) // Définit le nom de la commande
            ->setDescription('Remplir les tables des dates en hébreu.')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Vider les tables qu'on va remplir
        $connexion = $this->em->getConnection();
        $platform = $connexion->getDatabasePlatform();

        // Vider la table annee
        $connexion->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
        $truncateSql = $platform->getTruncateTableSQL('annee');
        $connexion->executeStatement($truncateSql);
        $connexion->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');

        // Vider la table jourMois
        $connexion->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
        $truncateSql = $platform->getTruncateTableSQL('jour_mois');
        $connexion->executeStatement($truncateSql);
        $connexion->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');

        // Vider la table jourSemaine
        $connexion->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
        $truncateSql = $platform->getTruncateTableSQL('jour_semaine');
        $connexion->executeStatement($truncateSql);
        $connexion->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');

        // Vider la table mois
        $connexion->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
        $truncateSql = $platform->getTruncateTableSQL('mois');
        $connexion->executeStatement($truncateSql);
        $connexion->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');


        // remplir les tables

        // table annee
        // $annee = new Annee(5700, "חמשת אלפים ושבע מאות");
        // $this->em->persist($annee);

        $annee = new Annee(5778, "חמשת אלפים ושבע מאות ושבעים ושמונה");
        $this->em->persist($annee);

        $annee = new Annee(5782, "חמשת אלפים ושבע מאות ושמנים ושתיים");
        $this->em->persist($annee);

        $annee = new Annee(5783, "חמשת אלפים ושבע מאות ושמנים ושלש");
        $this->em->persist($annee);

        $annee = new Annee(5784, "חמשת אלפים ושבע מאות ושמנים וארבע");
        $this->em->persist($annee);

        $annee = new Annee(5785, "חמשת אלפים ושבע מאות ושמנים וחמש");
        $this->em->persist($annee);

        $annee = new Annee(5786, "חמשת אלפים ושבע מאות ושמנים ושש");
        $this->em->persist($annee);

        $annee = new Annee(5787, "חמשת אלפים ושבע מאות ושמנים ושבע");
        $this->em->persist($annee);

        $annee = new Annee(5788, "חמשת אלפים ושבע מאות ושמנים ושמנה");
        $this->em->persist($annee);

        $annee = new Annee(5789, "חמשת אלפים ושבע מאות ושמנים ותשע");
        $this->em->persist($annee);


        // table jourMois
        $jourMois = new JourMois(1, "באחד");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(2, "שני ימים");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(3, "שלשה ימים");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(4, "ארבעה ימים");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(5, "חמשה ימים");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(6, "ששה ימים");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(7, "שבעה ימים");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(8, "שמנה ימים");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(9, "תשעה ימים");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(10, "עשרה ימים");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(11, "אחד עשר יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(12, "שנים עשר יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(13, "שלשה עשר יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(14, "ארבעה עשר יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(15, "חמשה עשר יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(16, "ששה עשר יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(17, "שבעה עשר יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(18, "שמנה עשר יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(19, "תשעה עשר יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(20, "עשרים יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(21, "אחד ועשרים יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(22, "שנים ועשרים יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(23, "שלשה ועשרים יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(24, "ארבעה ועשרים יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(25, "חמשה ועשרים יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(26, "ששה ועשרים יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(27, "שבעה ועשרים יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(28, "שמנה ועשרים יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(29, "תשעה ועשרים יום");
        $this->em->persist($jourMois);

        $jourMois = new JourMois(30, "יום שלשים");
        $this->em->persist($jourMois);


        // table jourSemaine
        $jourSemaine = new JourSemaine(7, "dimanche", "אחד");
        $this->em->persist($jourSemaine);

        $jourSemaine = new JourSemaine(1, "lundi", "שני");
        $this->em->persist($jourSemaine);

        $jourSemaine = new JourSemaine(2, "mardi", "שלישי");
        $this->em->persist($jourSemaine);

        $jourSemaine = new JourSemaine(3, "mercredi", "רביעי");
        $this->em->persist($jourSemaine);

        $jourSemaine = new JourSemaine(4, "jeudi", "חמישי");
        $this->em->persist($jourSemaine);

        $jourSemaine = new JourSemaine(5, "vendredi", "ששי");
        $this->em->persist($jourSemaine);


        // table mois
        $mois = new Mois(1, "tichri", "תשרי");
        $this->em->persist($mois);

        $mois = new Mois(2, "hechvan", "מרחשון");
        $this->em->persist($mois);

        $mois = new Mois(3, "kislev", "כסלו");
        $this->em->persist($mois);

        $mois = new Mois(4, "tevet", "טבת");
        $this->em->persist($mois);

        $mois = new Mois(5, "chevat", "שבט");
        $this->em->persist($mois);

        $mois = new Mois(14, "adar", "אדר");
        $this->em->persist($mois);

        $mois = new Mois(6, "adar alef", "אדר הראשון");
        $this->em->persist($mois);

        $mois = new Mois(7, "adar bet", "אדר השני");
        $this->em->persist($mois);

        $mois = new Mois(8, "nissan", "ניסן");
        $this->em->persist($mois);

        $mois = new Mois(9, "iyar", "אייר");
        $this->em->persist($mois);

        $mois = new Mois(10, "sivan", "סיון");
        $this->em->persist($mois);

        $mois = new Mois(11, "tamouz", "תמוז");
        $this->em->persist($mois);

        $mois = new Mois(12, "av", "מנחם אב");
        $this->em->persist($mois);

        $mois = new Mois(13, "eloul", "אלול");
        $this->em->persist($mois);


        $this->em->flush();

        $output->writeln('Les tables se sont bien remplies.');

        return Command::SUCCESS;
    }
}
