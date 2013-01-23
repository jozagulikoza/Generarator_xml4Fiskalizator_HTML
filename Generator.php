<?php

include 'Fiskalizator.php';
// ili path u kom se nalati

class GeneratorXML 
{
    public $dirXMLDokumntata = '';
    public $datotekaOdgovora;
    public $datotekaGreske ='fiskal_errors.log';
    public $tns = 'http://www.apis-it.hr/fin/2012/types/f73';
    public $xsi='http://www.w3.org/2001/XMLSchema-instance';
    public $spremaDokumente = false;

    public $certPath = '';
    public $certPass = '';
    public $errors = array();
    public $zki =''; 
    public $jir ='';
    
    function kreirajZahtjevRacuna(Racun $racun)
    {
        //$nazivXMLDok = $this->direktorijXMLRacuna . 'Racun' . $racun->oznPosPr . $racun->brOznRac . '.xml';
        $nazivXMLDok = 'Racun' . $racun->oznPosPr . $racun->brOznRac . '.xml';
        // PHP verzija s kojom radimo ima grešku u klasi xwriter 
        // funkcija openUri($nazivXMLDok) ne radi dobro ako u nazivu ima  direktorij
        if (!empty($this->dirXMLDokumntata))
        {
            $pwd = getcwd();
            chdir($this->dirXMLDokumntata);
        } 
        $noviXMLRacun = new XMLWriter();
        $noviXMLRacun->openUri($nazivXMLDok);

        $noviXMLRacun->startDocument('1.0','UTF-8');  
        $noviXMLRacun->setIndent(true);
        $noviXMLRacun->startElement("tns:RacunZahtjev");
        $noviXMLRacun->writeAttribute('xmlns:tns', $this->tns);
        $noviXMLRacun->writeAttribute('xmlns:xsi', $this->xsi);

        /*
        $noviXMLRacun->startElement('tns:Zaglavlje');
        $noviXMLRacun->writeElement('tns:IdPoruke', $this->generirajUUID());
        $noviXMLRacun->writeElement('tns:DatumVrijeme', date('d.m.Y\TH:m:s'));
        $noviXMLRacun->endElement(); //Zaglavlje
        */

        $noviXMLRacun->startElement('tns:Racun');
        $noviXMLRacun->writeElement('tns:Oib', $racun->oib);
        $noviXMLRacun->writeElement('tns:USustPdv', $racun->uSustPdv);

        $d1=new DateTime($racun->datVrijeme);
        $noviXMLRacun->writeElement('tns:DatVrijeme', $d1->format('d.m.Y\TH:m:s'));
        $noviXMLRacun->writeElement('tns:OznSlijed', $racun->oznSlijed);
        $noviXMLRacun->startElement("tns:BrRac");

        $noviXMLRacun->writeElement('tns:BrOznRac', $racun->brOznRac);
        $noviXMLRacun->writeElement('tns:OznPosPr', $racun->oznPosPr);
        $noviXMLRacun->writeElement('tns:OznNapUr', $racun->oznNapUr);
        $noviXMLRacun->endElement(); //BrRac

        if(isset($racun->pdv))
        {
            $noviXMLRacun->startElement("tns:Pdv");
            foreach ($racun->pdv as $lpdv)
            {
                $noviXMLRacun->startElement('tns:Porez');
                $noviXMLRacun->writeElement('tns:Stopa', number_format($lpdv['Stopa'],2, '.', ''));
                $noviXMLRacun->writeElement('tns:Osnovica', number_format($lpdv['Osnovica'],2, '.', ''));
                $noviXMLRacun->writeElement('tns:Iznos', number_format($lpdv['Iznos'],2, '.', ''));
                $noviXMLRacun->endElement(); 
            }
            $noviXMLRacun->endElement();    
        }
        if(isset($racun->pnp))
        {
            $noviXMLRacun->startElement("tns:Pnp");
            foreach ($racun->pnp as $lpdv)
            {
                $noviXMLRacun->startElement('tns:Porez');
                $noviXMLRacun->writeElement('tns:Stopa', number_format($lpdv['Stopa'],2, '.', ''));
                $noviXMLRacun->writeElement('tns:Osnovica', number_format($lpdv['Osnovica'],2, '.', ''));
                $noviXMLRacun->writeElement('tns:Iznos', number_format($lpdv['Iznos'],2, '.', ''));
                $noviXMLRacun->endElement(); 
            }
            $noviXMLRacun->endElement();    
        }
        if(isset($racun->ostaliPor))
        {
            $noviXMLRacun->startElement("tns:OstaliPor");
            foreach ($racun->ostaliPor as $lop)
            {
                $noviXMLRacun->startElement('tns:Porez');
                $noviXMLRacun->writeElement('tns:Naziv', $lop['Naziv']);
                $noviXMLRacun->writeElement('tns:Stopa', number_format($lop['Stopa'],2, '.', ''));
                $noviXMLRacun->writeElement('tns:Osnovica', number_format($lop['Osnovica'],2, '.', ''));
                $noviXMLRacun->writeElement('tns:Iznos', number_format($lop['Iznos'],2, '.', ''));
                $noviXMLRacun->endElement(); 
            }
            $noviXMLRacun->endElement();    
        }
        ///*
        if ($racun->iznosOslobPdv>0)
            $noviXMLRacun->writeElement('tns:IznosOslobPdv', number_format($racun->iznosOslobPdv,2, '.', ''));
        if ($racun->iznosMarza>0)
            $noviXMLRacun->writeElement('tns:IznosMarza', number_format($racun->iznosMarza,2, '.', ''));
        if ($racun->iznosNePodlOpor>0)
            $noviXMLRacun->writeElement('tns:IznosNePodlOpor', number_format($racun->iznosNePodlOpor,2, '.', ''));
        //*/
        if(isset($racun->naknade))
        {
            $noviXMLRacun->startElement("tns:Naknade");
            foreach ($racun->naknade as $n)
            {
                $noviXMLRacun->startElement('tns:Naknada');
                $noviXMLRacun->writeElement('tns:NazivN', $n['Naziv']);
                $noviXMLRacun->writeElement('tns:IznosN', number_format($n['Iznos'],2, '.', ''));
                $noviXMLRacun->endElement(); 
            }
            $noviXMLRacun->endElement();    
        }

        $noviXMLRacun->writeElement('tns:IznosUkupno', number_format($racun->iznosUkupno, 2, '.', ''));

        $noviXMLRacun->writeElement('tns:NacinPlac', $racun->nacinPlac);
        $noviXMLRacun->writeElement('tns:OibOper', $racun->oibOper);
        /*
        $noviXMLRacun->writeElement('tns:ZastKod', $this->zastitniKodIzracun( $this->certPath, //'fiskal 1.pfx', 
                $racun->lozinkaCert, 
                $racun->oib,
                $racun->datVrijeme, 
                $racun->brOznRac, 
                $racun->oznPosPr, 
                $racun->oznNapUr,
                $racun->iznosUkupno))
        */
        $noviXMLRacun->writeElement('tns:NakDost', $racun->nakDost);
        if ($racun->nakDost=='true')
        {
            $noviXMLRacun->writeElement('tns:ParagonBrRac', $racun->paragonBrRac);
            $noviXMLRacun->writeElement('tns:SpecNamj', $racun->specNamj);
        }


        $noviXMLRacun->endElement(); //racunZahtjev

        $noviXMLRacun->endDocument();
        $noviXMLRacun->flush();

        // PHP verzija s kojom radimo ima grešku u klasi xwriter 
        // funkcija openUri($nazivXMLDok) ne radi dobro ako u nazivu ima  direktorij
        if (!empty($this->dirXMLDokumntata))
        {
            chdir($pwd);
        } 
        return($this->dirXMLDokumntata.$nazivXMLDok);
    }

    function posaljiZahtjevPoslPr(PoslovniProstor $pp)
    {
        $ppNaziv = $this->kreirajZahtjevPoslPr($pp);
        return $this->posaljiZahtjev($ppNaziv);
    }

    function posaljiZahtjev($ppNaziv)
    {        
        $fis = new Fiskalizator();

        #UNCOMMENT FOLLOWING LINE AFTER YOU THOROUGHLY TESTED DEMO MODE (service provider says 2 days minimum)
        #$fis->setProductionMode();
        #Also, do not forget to change certPath and certPass to match your production certificate

        #Private key used to add your signature to xml request
        $fis->certPath = $this->certPath;
        $fis->certPass = $this->certPass;
        
        $doc = new DOMDocument();
        $doc->load($ppNaziv);
        $this->datotekaOdgovora = '';
        $odgovor = $fis->doRequest($doc);
        $this->errors = $fis->getErrors();
        $this->zki = $fis->getZKI(); 
        $this->jir = $fis->getJIR($fis->getResponse());
       if(!$this->spremaDokumente)
            unlink($ppNaziv);
        else if ($odgovor != false ) 
            $this->datotekaOdgovora =$this->snimiOdgovor($ppNaziv, $odgovor);
        if ($errors = $fis->getErrors() or $odgovor === false ) 
        {
            $this->saveErrors($doc);
            return false;
        }
        else 
        {
            return true;
        }  
    }

    function posaljiZahtjevRacuna(Racun $racun)
    {
        $ppNaziv = $this->kreirajZahtjevRacuna($racun);
        if($this->posaljiZahtjev($ppNaziv))
            return $this->jir;
        else
            return false;
    }

    function snimiOdgovor($nazivZahtjeva, $odgovor)
    {
        $nazivOdgovor =  str_replace('.xml', 'Odgovor.xml', $nazivZahtjeva);
        $domOdgovor = new DOMDocument();
        $domOdgovor->preserveWhiteSpace = false;
        $domOdgovor->formatOutput = true;
        $domOdgovor->loadXML($odgovor);
        $domOdgovor->save($nazivOdgovor);
        return $nazivOdgovor;
    }   

    function kreirajZahtjevPoslPr(PoslovniProstor $pp)
    {
        //$nazivXMLDok=$this->direktorijXMLRacuna . 'PoslPr' . $pp->oznPoslProstora. ($pp->oznakaZatvaranja=='Z'? 'Z':''). '.xml';
        $nazivXMLDok='PoslPr' . $pp->oznPoslProstora. ($pp->oznakaZatvaranja=='Z'? 'Z':''). '.xml';
        // PHP verzija s kojom radimo ima grešku u klasi xwriter 
        // funkcija openUri($nazivXMLDok) ne radi dobro ako u nazivu ima  direktorij
        if (!empty($this->dirXMLDokumntata))
        {
            $pwd = getcwd();
            chdir($this->dirXMLDokumntata);
        } 
        $noviXMLPoslPr = new XMLWriter();
        //echo $this->direktorijXMLRacuna . 'Racun' . $this->racun['OznPosPr'] . $this->racun['BrOznRac'] .'.xml';
        $noviXMLPoslPr->openUri($nazivXMLDok);

        $noviXMLPoslPr->startDocument('1.0','UTF-8');  
        $noviXMLPoslPr->setIndent(true);
        $noviXMLPoslPr->startElement("tns:PoslovniProstorZahtjev");
        $noviXMLPoslPr->writeAttribute('xmlns:tns', $this->tns);
        //$noviXMLPoslPr->writeAttribute('xmlns:xsi', $this->xsi);

        /*
        $noviXMLPoslPr->startElement('tns:Zaglavlje');
        $noviXMLPoslPr->writeElement('tns:IdPoruke', $this->generirajUUID());
        $noviXMLPoslPr->writeElement('tns:DatumVrijeme', date('d.m.Y\TH:m:s'));
        $noviXMLPoslPr->endElement(); //Zaglavlje
        */

        $noviXMLPoslPr->startElement('tns:PoslovniProstor');
        $noviXMLPoslPr->writeElement('tns:Oib', $pp->oib);
        $noviXMLPoslPr->writeElement('tns:OznPoslProstora', $pp->oznPoslProstora);

        $noviXMLPoslPr->startElement('tns:AdresniPodatak');
        if (strlen(trim($pp->ulica.$pp->kucniBroj.$pp->kucniBrojDodatak.$pp->kucniBrojDodatak.$pp->naselje.$pp->opcina))>0)
        {
            $noviXMLPoslPr->startElement('tns:Adresa');
            if (strlen(trim($pp->ulica))>0)
                $noviXMLPoslPr->writeElement('tns:Ulica', $pp->ulica);
            if (strlen(trim($pp->kucniBroj))>0)
                $noviXMLPoslPr->writeElement('tns:KucniBroj', $pp->kucniBroj);
            if (strlen(trim($pp->kucniBrojDodatak))>0)
                $noviXMLPoslPr->writeElement('tns:BrojPoste', $pp->kucniBrojDodatak);
            if (strlen(trim($pp->brojPoste))>0)
                $noviXMLPoslPr->writeElement('tns:BrojPoste', $pp->brojPoste);
            if (strlen(trim($pp->naselje))>0)
                $noviXMLPoslPr->writeElement('tns:Naselje', $pp->naselje);
            if (strlen(trim($pp->opcina))>0)
                $noviXMLPoslPr->writeElement('tns:Opcina', $pp->opcina);
            $noviXMLPoslPr->endElement(); //Adresa
        }
        if (strlen(trim($pp->ostaliTipoviPP))>0)
            $noviXMLPoslPr->writeElement('tns:OstaliTipoviPP', $pp->ostaliTipoviPP);
        $noviXMLPoslPr->endElement(); //AdresniPodatak

        $noviXMLPoslPr->writeElement('tns:RadnoVrijeme', $pp->radnoVrijeme);
        $d1=new DateTime($pp->datumPocetkaPrimjene);
        $noviXMLPoslPr->writeElement('tns:DatumPocetkaPrimjene', $d1->format('d.m.Y'));
        //if ($pp->oznakaZatvaranja=='Z')
        if (!empty($pp->oznakaZatvaranja))
            $noviXMLPoslPr->writeElement('tns:OznakaZatvaranja', $pp->oznakaZatvaranja);
        if (strlen(trim($pp->specNamj))>0)
            $noviXMLPoslPr->writeElement('tns:SpecNamj', $pp->specNamj);


        $noviXMLPoslPr->endElement(); //PoslovniProstor

        $noviXMLPoslPr->endDocument();
        $noviXMLPoslPr->flush();
        if (!empty($this->dirXMLDokumntata))
        {
            chdir($pwd);
        } 
        return($this->dirXMLDokumntata.$nazivXMLDok);
    }

    private function saveErrors(DOMDocument $doc)
    {
        if(empty($this->datotekaGreske)) return;

        if (!($fp = fopen($this->datotekaGreske, 'a'))) 
        {
            echo 'Nemogu otvoriti log datoteku:'.$this->datotekaGreske.'<br>';
            return;
        }
        fprintf($fp, "---------- Vrijeme nastanka greške: %s\nGreška: \n", date('d.m.Y H:m:s'));

        $errors = $this->errors ;
        foreach ($errors as $error)
            fprintf ($fp, htmlspecialchars($error)."\n");

        fprintf($fp, "Dukument zahtjeva:\n");
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        fprintf($fp, $doc->saveXML());
        fclose($fp);

    }   
    
}

class PoslovniProstor
{
    var $oib ='';
    var $oznPoslProstora ='';
    var $ulica ='';
    var $kucniBroj ='';
    var $kucniBrojDodatak ='';
    var $brojPoste ='';
    var $naselje ='';
    var $opcina ='';
    var $ostaliTipoviPP ='';
    var $radnoVrijeme ='';
    var $datumPocetkaPrimjene ='';
    var $specNamj ='';
    var $oznakaZatvaranja ='';
}

class Racun
{
    var 
    $oib ='',      
    $uSustPdv = '',
    $datVrijeme = '',
    $oznSlijed = '',
    $brOznRac = '',
    $oznPosPr ='', 
    $oznNapUr = '',
    $iznosOslobPdv = 0,
    $iznosMarza = 0,
    $iznosNePodlOpor = 0,
    $iznosUkupno = 0,
    $nacinPlac = '',
    $oibOper = '',
    $nakDost = '',
    $paragonBrRac = '',
    $specNamj = '';
    var $pnp; // = array();
        //= array ( array ('Stopa' => 3, 'Osnovica' => 10, 'Iznos' => 0.3));
    var $ostaliPor; // = array() ;
        //= array (array ('Naziv' => 'Porez na luksuz', 'Stopa' => 25, 'Osnovica' => 100, 'Iznos' => 25));
    var $naknade; // = array();
        //= array (array ('Naziv' => 'Povratna naknada', 'Iznos' => 45.37));
    var $pdv; // = array();
       // = array ( array ('Stopa' => 25.0, 'Osnovica' => 100.0, 'Iznos' => 25.0),
         //   array ('Stopa' => 7.0, 'Osnovica' => 100.0, 'Iznos' => 7.0));
    
    function dodajPdv($stopa, $osnovica, $iznos)
    {
        $this->pdv[] =  array ('Stopa' => $stopa, 'Osnovica' => $osnovica, 'Iznos' => $iznos);
    }
           
    function dodajPnp($stopa, $osnovica, $iznos)
    {
        $this->pnp[] =  array ('Stopa' => $stopa, 'Osnovica' => $osnovica, 'Iznos' => $iznos);
        
    }

    function dodajOstaliPor($naziv, $stopa, $osnovica, $iznos)
    {
         $this->ostaliPor[] =  array ('Naziv' => $naziv, 'Stopa' => $stopa, 'Osnovica' => $osnovica, 'Iznos' => $iznos);
    }

    function dodajNaknadu($naziv, $iznos)
    {
        $this->naknade[] =  array ('Naziv' => $naziv, 'Iznos' => $iznos);       
    }
    
}
?>
