package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.TopicVO;

	public class TopicService extends AbstractService
	{
		public function TopicService()
		{
			super("TopicService", "http://aerial-test/server.php", TopicVO);
		}
	}
}