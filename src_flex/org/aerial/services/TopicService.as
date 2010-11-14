package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.TopicVO;
	import org.aerial.config.Config;

	public class TopicService extends AbstractService
	{
		public function TopicService()
		{
			super("TopicService", Config.SERVER_URL, TopicVO);
		}
	}
}