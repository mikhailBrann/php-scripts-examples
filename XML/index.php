<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class XMLWorker
{
    private $currentFile = false;
    private $newFile = false;
     function __construct($xmlPath)
    {
        $this->currentFile = simplexml_load_file($xmlPath);
    }

    protected function getCurrentFile(): SimpleXMLElement
    {
        return $this->currentFile;
    }

    protected function createNewFile()
    {
        $fileForm = <<<XML
        <?xml version='1.0' encoding="UTF-8"?>
        <movies>
        </movies>
        XML;
        $this->newFile = new SimpleXMLElement($fileForm);

        return $this->newFile;
    }
}

class FilmWorker extends XMLWorker
{
    public function searchFilms(string $type='', int $age=0)
    {
        $xml = $this->getCurrentFile();
        $movies = $xml->item;
        $filmListResult = [];
        $count = 0;

        foreach ($movies as $movie) {
            $isType = mb_strtolower($movie->type) == mb_strtolower($type);

            preg_match_all(
                '/^«([0-9]*)\+»/m',
                $movie->_age_limit,
                $matches,
                PREG_SET_ORDER,
                0
            );

            $reciveAgeResult = intval($matches[0][1]);

            if($isType && $reciveAgeResult >= $age) {
                $filmListResult[] = [
                    'ID' => $count,
                    'TITLE' => $movie->film,
                    'DISTRIB_ID' => $movie->_distribution_id,
                    'TYPE' => $movie->type,
                    'AGE' => $movie->_age_limit
                ];
                $count++;
            }
        }

        return $filmListResult;
    }

    public function safeResult(string $type='', int $age=0)
    {
        $newMovieList = $this->createNewFile();
        $movieFilterResult = $this->searchFilms($type, $age);

        foreach ($movieFilterResult as $film) {
            $movie = $newMovieList->addChild('movie');
            $movie->addChild('id', $film['ID']);

            if (!empty($film['TITLE'])) $movie->addChild('title', $film['TITLE']);
            if (!empty($film['DISTRIB_ID'])) $movie->addChild('_distrib_id', $film['DISTRIB_ID']);
            if (!empty($film['TYPE'])) $movie->addChild('type', $film['TYPE']);
            if (!empty($film['AGE'])) $movie->addChild('age', $film['AGE']);
        }

        $newFilePath = __DIR__ . '/result.xml';

        $newMovieList->asXML($newFilePath);
        return $newMovieList;
    }
}

$pathToFile = __DIR__ . '/data-films.xml';
$xml = new FilmWorker($pathToFile);
$result = $xml->safeResult('художественный', 18);
