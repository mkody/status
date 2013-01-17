<?php
	/**
	 * Minecraft PHP Query Class
     * Copyright (C) 2012 Scott Reed (h02)
     * 
     * This program is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     * 
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     * 
     * You should have received a copy of the GNU General Public License
     * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	class MinecraftQuery 
	{
		/**
		 * Queries a Minecraft server
		 *
		 * @static
		 * @param string $address - IP address to the Minecraft server.
		 * @param int $port - Port of the Minecraft server.
		 * @param int $timeout - Time until giving up on connecting to the Minecraft server.
		 * @return array|bool - An array on success, FALSE on failure.
		*/
		public static function query($address, $port = 25565, $timeout = 2) 
		{
			$socket = @fsockopen($address, $port, $errno, $errstr, $timeout);

			if (!$socket) return false;

			fwrite($socket, chr(254));

			$response = "";
			
			while(!feof($socket)) $response .= fgets($socket, 1024);

			$result = array();
			$response = str_replace(chr(0),"",$response);
			$response = substr($response, 2);
			$query = preg_split("/[".chr(167)."]/", $response, -1, PREG_SPLIT_NO_EMPTY);
			
			$result['hostname'] 	= trim($query[0]);
			$result['players']		= (int) $query[1];
			$result['maxplayers'] 	= (int) $query[2];
			return $result;
		}
	}
?>