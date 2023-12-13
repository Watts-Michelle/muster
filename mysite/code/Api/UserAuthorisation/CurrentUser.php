<?php


class CurrentUser
{
	/** @var  Member */
	private static $user;

	public static function setUser(Member $user)
	{
		self::$user = $user;
	}

	public static function getUser()
	{
		return self::$user;
	}

	public static function getUserID()
	{
		return self::$user->ID;
	}

	public static function getUserUUID()
	{
		return self::$user->UUID;
	}

}