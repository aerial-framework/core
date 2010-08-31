package model.services
{
	import com.forum.config.Config;
	
	import model.vo.TopicVO;
	
	import org.aerial.rpc.AbstractService;
	
	public class Test2Service extends AbstractService
	{
		public function Test2Service()
		{
			var conf:Config = new Config();
			super("TestService", conf.AMFGatewayURL, TopicVO);
		}
		
	
		
		
	}
}