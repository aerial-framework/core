package model.vo
{
	import models.base.BaseUser;
	
	[RemoteClass(alias="model.vo.User")]	
	[Bindable]
	public class User extends BaseUser
	{		
		public function User()
		{
		}
	}
}