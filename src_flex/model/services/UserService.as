package model.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import model.vo.UserVO
	import com.forum.config.Config;

	public class UserService extends AbstractService
	{
		public function UserService()
		{
			super("UserService", IConfig(new Config()).AMF_GATEWAY, UserVO);
		}
	}
}