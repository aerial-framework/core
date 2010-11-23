package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.UserVO;

	public class UserService extends AbstractService
	{
		public function UserService()
		{
			super("UserService", "http://aerial-test/server.php", UserVO);
		}
	}
}