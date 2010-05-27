package model.vo
{
	import model.vo.base.BaseUser;
	
	[RemoteClass(alias="model.vo.User")]	
	[Bindable]
	public class User extends BaseUser
	{		
		public function User()
		{
		}
	}
}