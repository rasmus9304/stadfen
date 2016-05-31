<?php
require_once(dirname(__FILE__) ."/messages.php");
class Conversation
{
	public static function GetConversationObj($ConversationID)
	{
		global $DB;
		$st = $DB->prepare("SELECT * FROM Conversations WHERE ID=? AND `Deleted`=0;");
		$st->execute(array($ConversationID));
		
		if($st->rowCount() > 0)
			return $st->fetchObject();
		else
			return NULL;
	}
	
	public static function GetConversationObj2($CustomerID, $Number)
	{
		global $DB;
		$st = $DB->prepare("SELECT * FROM Conversations WHERE CustomerID=? AND `Number`=? AND `Deleted`=0;");
		$st->execute(array($CustomerID,$Number));
		
		if($st->rowCount() > 0)
			return $st->fetchObject();
		else
			return NULL;
	}
	
	public static function Create($CustomerID, $Number, $Name=NULL,$LastMessageID=NULL,$LastMessageTime=NULL)
	{
		global $DB;
		$st = $DB->prepare("INSERT INTO `Conversations`(`CustomerID`, `Number`, `ConversationName`, `Created`, `Archived`,`LastMessageID`,`LastMessageTime`,`LastUpdateTime`) VALUES (?,?,?,?,?,?,?,?)");
		$st->execute(array($CustomerID,$Number,$Name,date("Y-m-d H:i:s"), 0,$LastMessageID,$LastMessageTime,date("Y-m-d H:i:s")));
		return $DB->lastInsertId();
	}
	
	public static function SetLastMessageID($ConversationID, $MessageID, $Time)
	{
		global $DB;
		$st = $DB->prepare("UPDATE `Conversations` SET `LastMessageID`=?,`LastMessageTime`=?,`LastUpdateTime`=?  WHERE ID=?");
		$st->execute(array($MessageID, $Time, date("Y-m-d H:i:s") ,$ConversationID));
	}
	
	public static function IncreaseNewMessageCount($ConversationID, $Increase)
	{
		global $DB;
		$st = $DB->prepare("UPDATE `Conversations` SET `NewMessageCount`=`NewMessageCount`+?,`LastUpdateTime`=? WHERE ID=?");
		$st->execute(array($Increase,date("Y-m-d H:i:s"),$ConversationID));
	}
	
	public static function GetConversationLabel($StandardNumber, $Name, $Nickname)
	{
		if($Name && $Nickname)
			return $Nickname . " (". $Name .")";
		else if($Nickname)
			return $Nickname;
		else if($Name)
			return $Name;
		else
			return Phonenumber::GetDisplayStyle($StandardNumber);
	}
	
	public static function SetActivation($ConversationID, $AccountID, $Active)
	{
		global $DB;
		
		//Check if conversationaccount row exists
		$ST = $DB->prepare("SELECT ID FROM `ConversationAccounts` WHERE `AccountID`=? AND `ConversationID`=?;");
		$ST->execute(array($AccountID,$ConversationID));
		$rowCount = $ST->rowCount();
		$ST->closeCursor();
		
		$_active = ($Active ? 1 : 0);
		
		//Update row if it exists, otherwise create it
		if($rowCount > 0)
		{
			//Update
			$ST = $DB->prepare("UPDATE `ConversationAccounts` SET Active=?, `LastUpdateTime`=? WHERE `AccountID`=? AND `ConversationID`=?;");
			$ST->execute(array($_active, date("Y-m-d H:i:s"), $AccountID, $ConversationID));
		}
		else
		{
			$ST = $DB->prepare("INSERT INTO `ConversationAccounts`(`AccountID`, `ConversationID`, `Active`,`LastUpdateTime`) VALUES (?,?,?,?);");
			$ST->execute(array($AccountID,  $ConversationID, $_active, date("Y-m-d H:i:s")));
		}
	}
	
	public static function SetArchiveStatus($ConversationID, $Archived)
	{
		global $DB;
		
		$ST = $DB->prepare("UPDATE Conversations SET Archived=?,`LastUpdateTime`=? WHERE ID=?;");
		$_archived = $Archived ? 1 : 0;
		$ST->execute(array($_archived,date("Y-m-d H:i:s"),$ConversationID));
	}
	
	public static function SetFavorite($ConversationID, $AccountID, $Favorite)
	{
		global $DB;
		
		$ST = $DB->prepare("SELECT ID FROM `ConversationAccounts` WHERE `AccountID`=? AND `ConversationID`=?;");
		$ST->execute(array($AccountID,$ConversationID));
		$rowCount = $ST->rowCount();
		$ST->closeCursor();
		
		$_favorite = ($Favorite ? 1 : 0);
		
		if($rowCount > 0)
		{
			//Update
			$ST = $DB->prepare("UPDATE `ConversationAccounts` SET Favorite=?,`LastUpdateTime`=? WHERE `AccountID`=? AND `ConversationID`=?;");
			$ST->execute(array($_favorite,date("Y-m-d H:i:s"),$AccountID,$ConversationID));
		}
		else
		{
			$ST = $DB->prepare("INSERT INTO `ConversationAccounts`(`AccountID`, `ConversationID`, `Favorite`,`LastUpdateTime`) VALUES (?,?,?,?);");
			$ST->execute(array($AccountID,$ConversationID,$_favorite,date("Y-m-d H:i:s")));
		}
		
		$com->Data->Success = TRUE;
	}
	
	public static function SetName($ConversationID, $Name)
	{
		global $DB;
		
		$_name = trim($Name);
		if(empty($_name))
			$_name = NULL;
		
		$ST = $DB->prepare("UPDATE Conversations SET ConversationName = ? ,`LastUpdateTime`=? WHERE ID = ?;");
		$ST->execute(array($_name,date("Y-m-d H:i:s"),$ConversationID));
		
		
	}
	
	public static function SetNickname($ConversationID, $AccountID, $Nickname)
	{
		global $DB;
		
		//Check if conversationaccount row exists
		$ST = $DB->prepare("SELECT ID FROM `ConversationAccounts` WHERE `AccountID`=? AND `ConversationID`=?;");
		$ST->execute(array($AccountID,$ConversationID));
		$rowCount = $ST->rowCount();
		$ST->closeCursor();
		
		$_nickname = trim($Nickname);
		if(empty($_nickname))
			$_nickname = NULL;
		
		if($rowCount > 0)
		{
			//Update
			
			//$com->EchoDebug("Updaterar nickname ConvID=". $_POST[F_CONVID] .", nickname=" . $nickname);
			$ST = $DB->prepare("UPDATE `ConversationAccounts` SET `Nickname`=?, `LastUpdateTime`=? WHERE `AccountID`=? AND `ConversationID`=?;");
			$ST->execute(array($_nickname,date("Y-m-d H:i:s"),$AccountID,$ConversationID));
		}
		else
		{
			//$com->EchoDebug("skapar rad med nickname ConvID=". $_POST[F_CONVID] .", nickname=" . $nickname);
			$ST = $DB->prepare("INSERT INTO `ConversationAccounts`(`AccountID`, `ConversationID`, `Nickname`,`LastUpdateTime`) VALUES (?,?,?,?);");
			$ST->execute(array($AccountID,$ConversationID,$_nickname,date("Y-m-d H:i:s")));
		}
		
		
	}
	
	public static function Remove($ConversationID)
	{
		global $DB;
		
		//Get the customer id associated with this conversation
		$ConversationObj = Conversation::GetConversationObj($ConversationID);
		$CustomerID = $ConversationObj->CustomerID;
		
		//Remove messages
		$ST = $DB->prepare("DELETE FROM Messages WHERE ConversationID=?;");
		$ST->execute(array($ConversationID));
		$ST->closeCursor();
		
		//Remove conversation
		$ST = $DB->prepare("DELETE FROM Conversations WHERE ID = ?;");
		$ST->execute(array($ConversationID));
		$ST->closeCursor();
		
		//Remove conversationaccounts
		$ST = $DB->prepare("DELETE FROM ConversationAccounts WHERE ConversationID = ?;");
		$ST->execute(array($ConversationID));
		$ST->closeCursor();
		
		//Add to deleted-conversations table
		$ST = $DB->prepare("INSERT INTO `DeletedConversations`(`ConversationID`, `DeleteTime`, `CustomerID`) VALUES (?,?,?);");
		$ST->execute(array($ConversationID,date("Y-m-d H:i:s"),$CustomerID));
		
		$com->Data->Success = TRUE;
	}
}