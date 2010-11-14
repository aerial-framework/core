package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.UserVO;
	import org.aerial.config;

	public class UserService extends AbstractService
	{
		public function UserService()
		{
			super("UserService", IConfig(new Config()).AMF_GATEWAY, UserVO);
		}
	}
}