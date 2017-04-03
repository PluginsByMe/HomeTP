<?php

namespace HomeTP;

use pocketmine\command\{Command, CommandSender};
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\level\{Position, Level};
use pocketmine\utils\TextFormat as C;
use pocketmine\math\Vector3;

class HomeTP extends PluginBase{
    
    public $homeData;
    
    public function onEnable(){
        $this->saveResource("homes.yml");
        @mkdir($this->getDataFolder());
        $this->homeData = new Config($this->getDataFolder()."homes.yml", Config::YAML, array());
        $this->getLogger()->info(C::GREEN."HomeTeleporter Version 3.2 has successfully loaded!");
        $this->getLogger()->info(C::YELLOW."Config saved!");
    }
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        switch(strtolower($command->getName())){
            
            case "home":
            if($sender instanceof Player && $sender->isOp()){
                $home = $this->homeData->get($sender.'-'.$args[0]);
                if($home["world"] instanceof Level){
                    $sender->setLevel($home["world"]);
                    $sender->teleport(new Position($home["x"], $home["y"], $home["z"]));
                    $sender->sendMessage(C::BLUE."You teleported home.");
                }else{
                    $sender->sendMessage(C::RED."That world is not loaded or your home doesn't exist!");
                }
                $sender->sendMessage(C::RED."You must be an op to issue this command");
            }else{
                if(!$sender->isOp()){
                    $sender->sendMessage(C::RED."Please run command in-game.");
                }
            }
            break;
            
            case "sethome":
            if ($sender instanceof Player){
                if($sender->isOp()){
                    $x = $sender->x;
                    $y = $sender->y;
                    $z = $sender->z;
                    $level = $sender->getLevel();
                    // $args[0] is the Name of the house -> /sethome <name>
                    $this->homeData->set($sender.'-'.$args[0], array(
                        "x" => $x,
                        "y" => $y,
                        "z" => $z,
                        "world" => $level,
                    ));
                    $sender->sendMessage(C::GREEN."Your home is set at coordinates\n" . "X:" . C::YELLOW . $x . C::GREEN . "\nY:" . C::YELLOW . $y . C::GREEN . "\nZ:" . C::YELLOW . $z . C::GREEN . "\nUse /home < ". $args[0] ." > to teleport to this home!");
                    $this->getLogger()->info($sender->getName() . " has set their home in world " . $sender->getLevel()->getName());
                }
                $sender->sendMessage(C::RED."You must be an op to issue this command");
            }
            $sender->sendMessage(C::RED. "Please run command in game.");
            break;
            
            case "ishome":
                $home = $this->homeData->get($args[0]);
                if($home["world"] instanceof Level){
                    $sender->sendMessage(C::BLUE."Yes, " . $args[0] . "is a house. It's location is " . $home["x"] ." " . $home["y"] . " " . $home["z"]. "In the world " . $home["world"]);
                }else{
                    $sender->sendMessage(C::BLUE. "No,-" . $args[0] . "-is not a house. Use the /sethome command to set a home with this name.");
                }
            break;
        }
        return true;
    }
        
    public function onDisable(){
        $this->getLogger()->info(C::RED. "HomeTeleporter has successfully Disabled!");
        $this->saveResource("homes.yml");
        $this->getLogger()->info(C::YELLOW."All homes have saved!");
    }
}
