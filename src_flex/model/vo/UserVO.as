package model.vo
{
	import model.vo.base.BaseUser;
	
	[RemoteClass(alias="model.vo.User")]	
	[Bindable]
	public class UserVO extends BaseUser
	{		
		public function UserVO()
		{
		}
	}
}