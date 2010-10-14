package model.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import model.vo.TopicVO
	import com.forum.config.Config;

	public class TopicService extends AbstractService
	{
		public function TopicService()
		{
			super("TopicService", IConfig(new Config()).AMF_GATEWAY, TopicVO);
		}
	}
}