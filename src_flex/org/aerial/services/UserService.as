package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.UserVO;
	import org.aerial.bootstrap.Aerial;

	public class UserService extends AbstractService
	{
		public function UserService()
		{
			super("UserService", Aerial.SERVER_URL, UserVO);
		}
	}
}