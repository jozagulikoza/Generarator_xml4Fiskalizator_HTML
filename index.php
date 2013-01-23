<?php
 
include 'Generator.php';
        error_reporting (E_ALL ^ E_NOTICE);
    
        $gen = new GeneratorXML();
        $gen->spremaDokumente = true;
        $gen->certPath = 'certifikat';
        $gen->certPass = 'pass';
        $gen->dirXMLDokumntata = './xmldokumenti/';
        // 
        $pp = new PoslovniProstor();
        $pp->oib ='0ib';
        $pp->oznPoslProstora ='2301';
        $pp->ulica ='Vladimira Nazora';
        $pp->kucniBroj ='18';
        $pp->kucniBrojDodatak ='';
        $pp->brojPoste ='40000';
        $pp->naselje ='Čakovec';
        $pp->opcina ='Čakovec';
        $pp->ostaliTipoviPP ='';
        $pp->radnoVrijeme ='Od 7 do 14 sati';
        $pp->datumPocetkaPrimjene ='2013-01-01';
        $pp->specNamj ='neka namjena ';
        $pp->oznakaZatvaranja ='';

        echo 'Šaljem zahtjev za otvaranje/promjenu poslovnog prostora:</br>';
        var_dump($pp);
        $odgovor = $gen->posaljiZahtjevPoslPr($pp);

        if(!$odgovor)
        {
            echo 'Doslo je do greške:</br>';
            $errors = $gen->errors;
            foreach ($errors as $error)
                echo 'Error ==> "'.htmlspecialchars($error).'<br>';
            echo 'Detaljan opis greške nalazi se u: '.$gen->datotekaGreske.'<br>';
        }
        else 
            echo 'Zahtjev uspješno izvršen.<br>';
        echo 'Odgovor se nalazi u datoteci: '.$gen->datotekaOdgovora.'<br>';

        // /* 
        $racun = new Racun();
        $racun->oib ='oib';
        $racun->uSustPdv = 'true';
        $racun->datVrijeme = '2012-01-01 12:00:00';
        $racun->oznSlijed = 'P';
        $racun->brOznRac = '12';
        $racun->oznPosPr ='2310';
        $racun->oznNapUr = '1';
        $racun->iznosOslobPdv = 4;
        $racun->iznosMarza = 10;
        $racun->iznosNePodlOpor = 10;
        $racun->iznosUkupno = 1340;
        $racun->nacinPlac = 'G';
        $racun->oibOper = 'oib';
        $racun->nakDost = 'false';
        $racun->paragonBrRac = '12';
        $racun->specNamj = 'neka namjena';
        $racun->dodajPdv(10, 300, 30);
        $racun->dodajPdv(25, 1000, 250);
        $racun->dodajPnp(8.52, 1000, 85.2);
        $racun->dodajNaknadu('Povratna naknada', 34.67);
        $racun->dodajOstaliPor('Porez na luksuz' , 25, 1000, 250);
        

        echo '<br>-----------<br>Šaljem zahtjev za račun:<br>';
        var_dump($racun);
        $odgovor = $gen->posaljiZahtjevRacuna($racun);
       if(!$odgovor)
        {
            echo 'Doslo je do greške:</br>';
            $errors = $gen->errors;
            foreach ($errors as $error)
                echo 'Error ==> "'.htmlspecialchars($error).'<br>';
            echo 'Detaljan opis greške nalazi se u: '.$gen->datotekaGreske.'<br>';
        }
        else 
            echo 'Zahtjev uspješno izvršen.<br>';
        
        echo 'JIR : '.$odgovor.'<br>ZKI: '. $gen->zki.'<br>';
        echo 'Odgovor se nalazi u datoteci: '.$gen->datotekaOdgovora.'<br>';    
?>
