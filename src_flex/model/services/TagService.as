package model.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import model.vo.TagVO
	import com.forum.config.Config;

	public class TagService extends AbstractService
	{
		public function TagService()
		{
			super("TagService", IConfig(new Config()).AMF_GATEWAY, TagVO);
		}
	}
}