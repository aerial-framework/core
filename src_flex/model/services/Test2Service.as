package model.services
{
	import com.forum.config.Config;
	
	import model.vo.TopicVO;
	
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	public class Test2Service extends AbstractService
	{
		public function Test2Service()
		{
			super("TestService", IConfig(new Config()).AMF_GATEWAY, TopicVO);
		}
		
	}
}