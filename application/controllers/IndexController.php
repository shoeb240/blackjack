<?php
/**
 * All Blackjack actions
 * 
 * @category   Application
 * @package    Application_Controller
 * @author     Shoeb Abdullah <shoeb240@gmail.com>
 * @copyright  Copyright (c) 2014, Shoeb Abdullah
 * @uses       Zend_Controller_Action
 * @version    1.0
 */
class IndexController extends Zend_Controller_Action
{
    /**
     * @var Zend_Session_Namespace Session namespace 'blackjack'
     */
    private $_blackjackNamespace;
    
    /**
     * Initialize session and ajax context
     *
     * @return void
     */    
    public function init()
    {
        $this->_blackjackNamespace = new Zend_Session_Namespace('blackjack');

        // Allowing Actions to Respond To Ajax Requests
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('startup', 'json')
                    ->addActionContext('play', 'json')
                    ->addActionContext('stick', 'json')
                    ->addActionContext('twist', 'json')
                    ->addActionContext('reset', 'json')
                    ->addActionContext('info', 'json')
                    ->initContext();
    }
    
    /**
     * Home page default action
     *
     * @return void
     */    
    public function indexAction()
    {
        // Prepare 52 cards deck
        if (!$this->_blackjackNamespace->deck) {
            $this->prepareDeck();
        }
    }

    /**
     * Ajax page to get anytime card and score info at startup or page refresh
     *
     * @return void
     */
    public function startupAction() {
        $playerPoints = $this->getCurrentPoints($this->getPlayerHand());
        $dealerPoints = $this->getCurrentPoints($this->getDealerHand());

        $result = $this->getResult($playerPoints, $dealerPoints);

        $this->view->playerHand =  $this->getPlayerHand();
        $this->view->dealerHand =  $this->getDealerHand();
        $this->view->playerPoints = $playerPoints;
        $this->view->dealerPoints = $dealerPoints;
        $this->view->result = $result;
        $this->view->history = $this->getHistory();
        $this->view->win = $this->getWins();
        $this->view->loose = $this->getLooses();
        $this->view->draw = $this->getDraws();

    }

    /**
     * Ajax page to handle Play button action
     *
     * @return void
     */
    public function playAction() {
        $this->_blackjackNamespace->playerHand = '';
        $this->_blackjackNamespace->dealerHand = '';
        
        $this->prepareDeck();
        $playerHand = $this->initialPlayerHand();
        $dealerHand = $this->initialDealerHand();

        $playerPoints = $this->getCurrentPoints($playerHand);
        $dealerPoints = $this->getCurrentPoints($dealerHand);

        $result = $this->getResult($playerPoints, $dealerPoints, true);

        $this->view->playerHand = $playerHand;
        $this->view->dealerHand = $dealerHand;
        $this->view->playerPoints = $playerPoints;
        $this->view->dealerPoints = $dealerPoints;
        $this->view->result = $result;
        $this->view->history = $this->getHistory();
        $this->view->win = $this->getWins();
        $this->view->loose = $this->getLooses();
        $this->view->draw = $this->getDraws();
    }

    /**
     * Ajax page to handle Stick button action
     *
     * @return void
     */
    public function stickAction() {
        do {
            $dealerHand = $this->dealerHand();
        } while ($this->getCurrentPoints($dealerHand) < 17);

        $playerPoints = $this->getCurrentPoints($this->getPlayerHand());
        $dealerPoints = $this->getCurrentPoints($this->getDealerHand());

        $result = $this->getResult($playerPoints, $dealerPoints, true);

        $this->view->playerHand = $this->getPlayerHand();
        $this->view->dealerHand = $this->getDealerHand();
        $this->view->playerPoints = $playerPoints;
        $this->view->dealerPoints = $dealerPoints;
        $this->view->result = $result;
        $this->view->history = $this->getHistory();
        $this->view->win = $this->getWins();
        $this->view->loose = $this->getLooses();
        $this->view->draw = $this->getDraws();
    }

    /**
     * Ajax page to handle Twist button action
     *
     * @return void
     */
    public function twistAction() {
        $this->playerHand();

        $playerPoints = $this->getCurrentPoints($this->getPlayerHand());
        $dealerPoints = $this->getCurrentPoints($this->getDealerHand());

        $result = $this->getResult($playerPoints, $dealerPoints, true);

        $this->view->playerHand = $this->getPlayerHand();
        $this->view->dealerHand = $this->getDealerHand();
        $this->view->playerPoints = $playerPoints;
        $this->view->dealerPoints = $dealerPoints;
        $this->view->result = $result;
        $this->view->history = $this->getHistory();
        $this->view->win = $this->getWins();
        $this->view->loose = $this->getLooses();
        $this->view->draw = $this->getDraws();
    }

    /**
     * Ajax page to reset all saved info
     *
     * @return void
     */
    public function resetAction() {
        $this->_blackjackNamespace->playerHand = '';
        $this->_blackjackNamespace->dealerHand = '';
        $this->_blackjackNamespace->deck = array();
        $this->_blackjackNamespace->history = array();
        $this->_blackjackNamespace->win = 0;
        $this->_blackjackNamespace->loose = 0;
        $this->_blackjackNamespace->draw = 0;
        $this->prepareDeck();

        $this->view->playerHand = $this->getPlayerHand();
        $this->view->dealerHand = $this->getDealerHand();
        $this->view->playerPoints = 0;
        $this->view->dealerPoints = 0;
        $this->view->result = 'stay';
        $this->view->history = $this->getHistory();
        $this->view->win = $this->getWins();
        $this->view->loose = $this->getLooses();
        $this->view->draw = $this->getDraws();
    }

    /**
     * Ajax page to get anytime info
     *
     * @return void
     */
    public function infoAction() {
        $playerPoints = $this->getCurrentPoints($this->getPlayerHand());
        $dealerPoints = $this->getCurrentPoints($this->getDealerHand());

        $result = $this->getResult($playerPoints, $dealerPoints);

        $this->view->playerHand = $this->getPlayerHand();
        $this->view->dealerHand = $this->getDealerHand();
        $this->view->playerPoints = $playerPoints;
        $this->view->dealerPoints = $dealerPoints;
        $this->view->result = $result;
        $this->view->history = $this->getHistory();
        $this->view->win = $this->getWins();
        $this->view->loose = $this->getLooses();
        $this->view->draw = $this->getDraws();
    }

    /**
     * Prepare deck before play
     *
     * @return void
     */
    private function prepareDeck() {
        $group = array('H', 'C', 'S', 'D');
        $faces = array(11 => 'J', 12 => 'Q', 13 => 'K');
        $num = 1;
        $deck = array();
        
        foreach ($group as $group) {
            $deck[$num]['name'] = 'A';
            $deck[$num]['value'] = 11;
            $deck[$num]['taken'] = false;
            $deck[$num]['group'] = $group;
            $num++;
            for ($i = 2; $i <= 10; $i++) {
                $deck[$num]['name'] = $i;
                $deck[$num]['value'] = $i;
                $deck[$num]['taken'] = false;
                $deck[$num]['group'] = $group;
                $num++;
            }
            for ($i = 11; $i <= 13; $i++) {
                $deck[$num]['name'] = $faces[$i];
                $deck[$num]['value'] = 10;
                $deck[$num]['taken'] = false;
                $deck[$num]['group'] = $group;
                $num++;
            }
        }
        $this->_blackjackNamespace->deck = $deck;
    }

    /**
     * Initial card distribution to player
     *
     * @return string $playerHand comma separated card index of deck
     */
    private function initialPlayerHand() {
        $playerCard[] = $this->getDeckCard();
        $playerCard[] = $this->getDeckCard();
        $this->_blackjackNamespace->playerHand = implode(',', $playerCard);

        return $this->_blackjackNamespace->playerHand;
    }

    /**
     * Initial card distribution to dealer
     *
     * @return string $dealerHand comma separated card index of deck
     */
    private function initialDealerHand() {
        $dealerCard[] = $this->getDeckCard();
        $this->_blackjackNamespace->dealerHand = implode(',', $dealerCard);

        return $this->_blackjackNamespace->dealerHand;

    }

    /**
     * Adding card to player hand
     *
     * @return string $playerHand comma separated card index of deck
     */
    private function playerHand() {
        $this->_blackjackNamespace->playerHand .= ',' . $this->getDeckCard();

        return $this->_blackjackNamespace->playerHand;
    }

    /**
     * Adding card to dealer hand
     *
     * @return string $dealerHand comma separated card index of deck
     */
    private function dealerHand() {
        $this->_blackjackNamespace->dealerHand .= ',' . $this->getDeckCard();
        
        return $this->_blackjackNamespace->dealerHand;
    }

    /**
     * Get card from deck
     *
     * @return int $rand card index of deck
     */
    private function getDeckCard() {
        do {
            $rand = rand(1, Zend_Registry::get('CARD_NUM'));
            $deckCard = $this->isDeckCard($rand);
        } while ($deckCard === false);

        $this->setTaken($rand);
        
        return $rand;
    }

    /**
     * Set card as used from deck
     *
     * @param int $cardNumber card index of deck
     * @return void
     */
    private function setTaken($cardNumber) {
        $this->_blackjackNamespace->deck[$cardNumber]['taken'] = false;
    }

    /**
     * Check if a card exists in deck
     *
     * @param int $cardNumber card index of deck
     * @return boolean
     */
    private function isDeckCard($cardNumber) {
        if ($this->_blackjackNamespace->deck[$cardNumber]['taken'] === false) return true;
        else return false;
    }

    /**
     * Calculate points for Hand
     *
     * @param string $hand comma separated card index of deck
     * @return int $points calculated points
     */
    private function getCurrentPoints($hand) {
        $handArr = explode(',', $hand);
        $points = 0;
        foreach ($handArr as $cardNumber) {
            $points += $this->_blackjackNamespace->deck[$cardNumber]['value'];
        }

        if ($this->checkAce($handArr) && $points > 21) $points = $points - 10;

        return $points;
    }

    private function checkAce($handArr) {
        $aceIndexes = array(1, 14, 27, 40);
        foreach($handArr as $card) {
            if (in_array($card, $aceIndexes)) return true;
        }
        return false;
    }

    private function getPlayerHand() {
        return $this->_blackjackNamespace->playerHand;
    }

    private function getDealerHand() {
        return $this->_blackjackNamespace->dealerHand;
    }

    /**
     * Define wiin, loose, draw or stay situation
     *
     * @param int $playerPoints player points
     * @param int $dealerPoints dealer points
     * @param boolean $save flag to save in history
     * @return string $result result of the game
     */
    private function getResult($playerPoints, $dealerPoints, $save = false) {
        $result = 'stay';
        if ($playerPoints == 21) {
            if ($dealerPoints == 21) {
                $result = 'draw';
            } else {
                $result = 'win';
            }
        } else if ($dealerPoints == 21) {
            $result = 'loose';
        } else if ($playerPoints > 21) {
            $result = 'loose';
        } else if ($dealerPoints > 21) {
            $result = 'win';
        } else if ($dealerPoints >= 17) {
            if ($playerPoints == $dealerPoints) {
                $result = 'draw';
            } else if ($playerPoints > $dealerPoints) {
                $result = 'win';
            } else {
                $result = 'loose';
            }
        }

        if (true === $save && ('win' == $result || 'loose' == $result || 'draw' == $result)) {
            $this->_blackjackNamespace->$result++;
            $this->_blackjackNamespace->history[] = array("playerPoints" => $playerPoints,
                                                          "dealerPoints" => $dealerPoints,
                                                          "result" => $result);
        }

        return $result;
    }

    private function getWins() {
        return $this->_blackjackNamespace->win;
    }

    private function getLooses() {
        return $this->_blackjackNamespace->loose;
    }

    private function getDraws() {
        return $this->_blackjackNamespace->draw;
    }

    private function getHistory() {
        return $this->_blackjackNamespace->history;
    }

}