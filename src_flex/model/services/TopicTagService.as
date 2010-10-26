package model.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import model.vo.TopicTagVO
	import com.forum.config.Config;

	public class TopicTagService extends AbstractService
	{
		public function TopicTagService()
		{
			super("TopicTagService", IConfig(new Config()).AMF_GATEWAY, TopicTagVO);
		}
	}
}