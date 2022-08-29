<?php namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;

class ShowMovies extends Command {

    public function configure(){
        $this->setName('show')
             ->addArgument('movieName',InputArgument::REQUIRED, 'Movie name.')
             ->addOption('plot',null,InputOption::VALUE_OPTIONAL,'Short or Full plot', 'short');
            }
    
    public function getMovies($movieName,$plot) {
        $apikey = "bee1f87e";
        $client = new Client([
            'base_uri' => 'http://www.omdbapi.com',
        ]);
        $response = $client->request('GET','',[
           'query' => [
            't' => $movieName,
            'apikey' => $apikey,
            'plot' => $plot,
           ] 
           ]);
        return json_decode($response->getBody(),true);
    }

    public function execute(InputInterface $input, OutputInterface $output){
        $movieName = $input->getArgument('movieName');
        $plot = $input->getOption('plot');
        $movie2 = $this->getMovies($movieName,$plot);
        $arrayForTable=[];
        foreach($movie2 as $name => $val) {
            if($name != "Ratings") {
                $arrayToPush = [$name,$val];
                array_push($arrayForTable,$arrayToPush);
            }
        }
        $table = new Table($output);
        $year= $movie2['Year'];
        $output->writeln("<info>$movieName - $year</info>");
        $table
              ->setRows($arrayForTable)
              ->render();
        
        return 0;
    }
}