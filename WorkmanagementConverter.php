<?php
require_once('conf.php');
require_once(INC_PATH.'Importer.class.php');

$channels = array(
    array(
        'importArgs' => array('channel_id' => 6316, 'name' => 'Workmanagement AG Rapperswil',),
        'readArgs' => array('url' => 'http://www.jobs.ch'),
    ),

    array(
        'importArgs' => array(	'channel_id' => 6317, 'name' => 'Workmanagement AG Glarus',),
        'readArgs' => array('url' => 'http://downloads.sputnik-it.com/sps_downloads/jobplattforms/jobch/5476/vacancies.xml'),
    ),

    array(
        'importArgs' => array(	'channel_id' => 6318, 'name' => 'Workmanagement AG Wetzikon',),
        'readArgs' => array('url' => 'http://downloads.sputnik-it.com/sps_downloads/jobplattforms/jobch/1118/vacancies.xml'),
    ),

    array(
        'importArgs' => array(	'channel_id' => 6319, 'name' => 'Workmanagement AG Pfäffikon SZ',),
        'readArgs' => array('url' => 'http://downloads.sputnik-it.com/sps_downloads/jobplattforms/jobch/1117/vacancies.xml'),
    ),
);

class WorkmanagementConverter extends Importer {
    function before_create(&$ad){
        $ad['bewerben_url'] = '';
    }
}


foreach($channels as $channel){
    echo "Processing ".$channel['importArgs']['name']."\n\n";

    try{
        $importArgsDefault = array(
            'logLevel' => 'DEBUG',
            'eqk-resistent' => 'partiell',
            'kontakt_name' => 'Fardin Asghari',
            'kontakt_email' => 'f.asghari@sputnik-it.com',
            'info_contact' => 'CC: m.schneider@workmanagement.ch',
            'on_error' => 'extend',
            'legacyLanguageCode' => false,
        );

        $importer = new WorkmanagementConverter(array_merge($importArgsDefault, $channel['importArgs']));

        $importer->setOrganisationMappingType("fix", array(12324, 5476, 1118, 1117));
        $input = $importer->read('url', $channel['readArgs']);
        $input = str_replace("\r\n", '', $input);
        $input = str_replace("<BRANCHE>:", "<BRANCHE>", $input);
        $input = str_replace(":</BRANCHE>", "</BRANCHE>", $input);

        $importer->import('jobs', $input);

    } catch (ImportException $e){
        echo "Import failed! ".$e->getMessage()."\n";
    }
    echo $importer->reportAsString();
    $importer->getReport()->close();
}
